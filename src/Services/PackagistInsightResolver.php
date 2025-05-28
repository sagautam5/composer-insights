<?php 

namespace ComposerInsights\Services;

class PackagistInsightResolver
{
    public function fetchMetaData(string $vendor, string $package): ?array
    {
        $url = "https://packagist.org/packages/{$vendor}/{$package}.json";
        $json = @file_get_contents($url, false, stream_context_create([
            'http' => ['header' => 'User-Agent: ComposerInsights']
        ]));

        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        return $data['package'] ?? null;
    }

    public function fetchLatestVersion(string $vendor, string $package): ?string
    {
        $url = "https://repo.packagist.org/p2/{$vendor}/{$package}.json";
        $json = @file_get_contents($url, false, stream_context_create([
            'http' => ['header' => 'User-Agent: ComposerInsights']
        ]));

        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        foreach ($data['packages']["{$vendor}/{$package}"] ?? [] as $versionData) {
            if (!isset($versionData['version_normalized']) || str_starts_with($versionData['version_normalized'], '9999999-dev')) {
                continue;
            }
            return $versionData['version'] ?? null;
        }

        return null;
    }
}