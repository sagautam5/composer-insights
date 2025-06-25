<?php 

namespace ComposerInsights\Support;

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
    }

    public function toArray(): array
    {
        return [
            'package' => $this->package,
            'license' => $this->license,
            'latestVersion' => $this->latestVersion,
            'usedVersion' => $this->usedVersion,
            'updatedAt' => $this->updatedAt,
            'latestRelease' => $this->latestRelease,
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
}