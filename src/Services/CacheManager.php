<?php 

namespace ComposerInsights\Services;

use Carbon\Carbon;

class CacheManager
{
    private string $cacheDir;

    public function __construct()
    {
        $this->cacheDir = DirectoryResolver::resolve('cache');

        DirectoryResolver::createDirectoryIfNotExists($this->cacheDir);
    }

    public function loadFromCache(string $name): ?array
    {
        $path = $this->getCachePath($name);
        
        if (!file_exists($path)) {
            return null;
        }

        $cache = json_decode(file_get_contents($path), true);

        if (isset($cache['__cached_at'])) {
            $cachedAt = Carbon::parse($cache['__cached_at']);
            if ($cachedAt->lt(Carbon::now()->subHours(24))) {
                return null;
            }
        }

        return $cache['data'] ?? null;
    }

    public function saveToCache(string $name, array $data): void
    {
        $payload = [
            '__cached_at' => Carbon::now()->toISOString(),
            'data' => $data
        ];
        
        $path = $this->getCachePath($name);

        DirectoryResolver::createDirectoryIfNotExists($path);

        file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT));
    }

    private function getCachePath(string $name): string
    {
        return "{$this->cacheDir}" . str_replace('/', '-', $name) . ".json";
    }
}