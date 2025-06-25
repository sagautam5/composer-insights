<?php

namespace ComposerInsights\Services;

class PackagistInsightResolver
{
    protected const USER_AGENT = 'ComposerInsights';

    public function fetchMetaData(string $vendor, string $package): ?array
    {
        $url = $this->buildMetadataUrl($vendor, $package);
        $data = $this->getJsonFromUrl($url);

        return $data['package'] ?? null;
    }

    public function fetchLatestVersion(string $vendor, string $package): ?string
    {
        $url = $this->buildVersionUrl($vendor, $package);
        $data = $this->getJsonFromUrl($url);

        $versions = $data['packages']["{$vendor}/{$package}"] ?? [];

        foreach ($versions as $versionData) {
            if ($this->isStableVersion($versionData)) {
                return $versionData['version'] ?? null;
            }
        }

        return null;
    }

    protected function buildMetadataUrl(string $vendor, string $package): string
    {
        return "https://packagist.org/packages/{$vendor}/{$package}.json";
    }

    protected function buildVersionUrl(string $vendor, string $package): string
    {
        return "https://repo.packagist.org/p2/{$vendor}/{$package}.json";
    }

    protected function getJsonFromUrl(string $url): ?array
    {
        $context = stream_context_create([
            'http' => ['header' => 'User-Agent: ' . self::USER_AGENT]
        ]);

        $json = file_get_contents($url, false, $context);

        if ($json === false) {
            throw new \RuntimeException("Failed to fetch data from URL: $url");
        }

        $decoded = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON from $url: " . json_last_error_msg());
        }

        return $decoded;
    }

    protected function isStableVersion(array $versionData): bool
    {
        if (!isset($versionData['version_normalized'])) {
            return false;
        }

        return !str_starts_with($versionData['version_normalized'], '9999999-dev');
    }
}
