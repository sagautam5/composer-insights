<?php 

namespace ComposerInsights\Services;

class ComposerDependencyLoader
{
    public function hasComposerFiles(): bool
    {
        return file_exists('composer.json') && file_exists('composer.lock');
    }
    
    public function loadComposerData(bool $includeDev, bool $excludeDev): array
    {
        if ($includeDev && $excludeDev) {
            throw new \InvalidArgumentException('Cannot include and exclude dev dependencies at the same time.');
        }

        $json = $this->decodeJsonFile('composer.json');
        $lock = $this->decodeJsonFile('composer.lock');

        $require = $json['require'] ?? [];
        $requireDev = $json['require-dev'] ?? [];

        $requires = match (true) {
            $excludeDev => array_keys($require),
            $includeDev => array_keys($requireDev),
            default => array_merge(array_keys($require), array_keys($requireDev)),
        };

        $requires = array_map('strtolower', $requires);
        $packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);

        return [$requires, $packages];
    }

    private function decodeJsonFile(string $filePath): array
    {
        return json_decode(file_get_contents($filePath), true);
    }
}