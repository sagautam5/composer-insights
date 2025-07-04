<?php 

namespace ComposerInsights\Support;
use ComposerInsights\Formatters\NumberFormatter;

class PackageInsight
{
    public string $package;
    public string $license;
    public string $latestVersion;
    public string $usedVersion;
    public string $updatedAt;
    public string $latestRelease;
    public string $downloads;
    public string $stars;
    public string $forks;
    public string $openIssues;
    public string $dependents;
    public string $suggesters;
    public bool $isStale;
    public bool $isOutdated;
    public bool $noReleaseInPastYear;
    
    public function __construct(public array $data)
    {    
        $this->package = $this->data['package']['name'];
        $this->license = $this->data['package']['license'];
        $this->latestVersion = $this->data['version']['latest'];
        $this->usedVersion = $this->data['version']['used'];
        $this->updatedAt = $this->data['maintenance']['updated_at'];
        $this->latestRelease = $this->data['release']['latest_at'] . ' | ' . $this->data['release']['time_since'];
        $this->downloads = $this->formatNumber($this->data['popularity']['downloads']);
        $this->stars = $this->formatNumber($this->data['popularity']['stars']);
        $this->forks = $this->formatNumber($this->data['popularity']['forks']);
        $this->openIssues = $this->formatNumber($this->data['health']['open_issues']);
        $this->dependents = $this->formatNumber($this->data['health']['dependents']);
        $this->suggesters = $this->formatNumber($this->data['health']['suggesters']);
        $this->isStale = $this->data['maintenance']['is_stale'];
        $this->isOutdated = $this->data['version']['is_outdated'];
        $this->noReleaseInPastYear = $this->data['release']['no_recent_release'];
    }

    public function headers(): array
    {
        return [
            'package_name',
            'package_license',
            'version_latest',
            'version_used',
            'version_is_outdated',
            'maintenance_updated_at',
            'maintenance_is_stale',
            'release_latest_at',
            'release_time_since',
            'popularity_downloads',
            'popularity_stars',
            'popularity_forks',
            'health_open_issues',
            'health_dependents',
            'health_suggesters',
        ];
    }

    public function toArray(): array
    {
        return [
            'package' => $this->package,
            'license' => $this->license,
            'latestVersion' => $this->formatLatestVersion(),
            'usedVersion' => $this->formatUsedVersion(),
            'updatedAt' => $this->formatUpdatedAt(),
            'latestRelease' => $this->formatLatestRelease(),
            'downloads' => $this->downloads,
            'stars' => $this->stars,
            'forks' => $this->forks,
            'openIssues' => $this->openIssues,
            'dependents' => $this->dependents,
            'suggesters' => $this->suggesters,
        ];
    }

    private function formatNumber($number): string
    {
        return NumberFormatter::humanize($number);
    }

    private function formatLatestRelease(): string
    {
        return $this->noReleaseInPastYear ? '<comment>' . $this->latestRelease . '</comment>' : $this->latestRelease;
    }

    private function formatLatestVersion(): string
    {
        return $this->isOutdated ? '<info>' . $this->latestVersion . '</info>' : $this->latestVersion;
    }

    private function formatUsedVersion(): string
    {
        return $this->isOutdated ? '<comment>' . $this->usedVersion . '</comment>' : $this->usedVersion;
    }

    private function formatUpdatedAt(): string
    {
        return $this->isStale ? '<comment>' . $this->updatedAt . '</comment>' : $this->updatedAt;
    }
}