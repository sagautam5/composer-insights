<?php 

namespace ComposerInsights\Services;

use Carbon\Carbon;
use Symfony\Component\Console\Output\OutputInterface;
class InsightCollector
{
    private GitHubAnalyzer $githubAnalyser;

    private PackagistInsightResolver $packagistInsightResolver;

    protected int $days;

    public function __construct(int $days = 180)
    {
        $this->githubAnalyser = new GitHubAnalyzer(getenv('GITHUB_TOKEN') ?? null, $days);
        $this->packagistInsightResolver = new PackagistInsightResolver();
        $this->days = $days;
    }
    public function collect(OutputInterface $output, array $packages, array $explicitRequires): array
    {
        $rows = [];
        foreach ($packages as $package) {
            $row = $this->analyzePackage($package, $explicitRequires, $output);
            
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function analyzePackage(array $package, array $explicitRequires, OutputInterface $output): ?array
    {        
        $name = strtolower($package['name']);
        
        if (!in_array($name, $explicitRequires, true)) {
            return null;
        }

        $repoUrl = $package['source']['url'] ?? '';
        if (!$repoUrl || !str_contains($repoUrl, 'github.com')) {
            $output->writeln("[SKIP] {$name} (non-GitHub)");
            return null;
        }

        $info = $this->githubAnalyser->fetchRepoData($repoUrl);
        if (isset($info['error'])) {
            $output->writeln("[ERROR] {$name}: {$info['error']}");
            return null;
        }

        $updatedAt = Carbon::parse($info['updated_at']);
        
        $metadata = $this->packagistInsightResolver->fetchMetaData(...explode('/', $name));
        
        $license = $this->getLicenseInfoFromMetaData($metadata);

        $latestVersion = $this->packagistInsightResolver->fetchLatestVersion(...explode('/', $name));

        $releaseData = $this->githubAnalyser->getReleaseData($repoUrl);

        return [
            'package' => [
                'name' => $name,
                'license' => $license
            ],
            'popularity' => [
                'stars' => $info['stargazers_count'] ?? 'N/A',
                'forks' => $info['forks_count'] ?? 'N/A',
                'downloads' => $metadata['downloads']['total'] ?? 'N/A',
            ],
            'release' => [
                'latest_at' => $releaseData['latest_at'] ?? 'N/A',
                'time_since' => $releaseData['time_since'] ?? 'N/A',
                'no_recent_release' => $releaseData['no_recent_release'] ?? 'N/A',
            ],
            'maintenance' => [
                'updated_at' => $updatedAt->diffForHumans(),
                'is_stale' => $updatedAt->lt(Carbon::now()->subDays($this->days)),
            ],
            'health' => [
                'dependents' => $metadata['dependents'] ?? 'N/A',
                'suggesters' => $metadata['suggesters'] ?? 'N/A',
                'open_issues' => $info['open_issues_count'] ?? 'N/A',
            ],
            'version' => [
                'latest' => $latestVersion ?? 'N/A',
                'used' => $package['version'] ?? 'N/A',
                'is_outdated' => $latestVersion == $package['version'] ? false : true
            ]
        ];
    }

    private function getLicenseInfoFromMetaData(array $metadata)
    {
        $license = $metadata['license'] ?? null;

        if (!$license && isset($metadata['versions'])) {
            foreach ($metadata['versions'] as $version) {
                if (!empty($version['license'])) {
                    $license = $version['license'];
                    break;
                }
            }
        }

        return is_array($license) ? implode(', ', $license) : ($license ?? 'N/A');
    }
}