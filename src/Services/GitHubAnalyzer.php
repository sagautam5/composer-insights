<?php

namespace ComposerInsights\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GitHubAnalyzer
{
    protected Client $client;

    public function __construct(?string $githubToken)
    {
        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
        ];

        if ($githubToken) {
            $headers['Authorization'] = "token {$githubToken}";
        }

        $this->client = new Client([
            'base_uri' => 'https://api.github.com/',
            'headers' => $headers,
        ]);
    }

    public function fetchRepoData(string $repoUrl): array
    {
        $ownerAndRepo = $this->extractOwnerRepo($repoUrl);

        if ($ownerAndRepo === null) {
            return ['error' => 'Invalid GitHub URL: ' . $repoUrl];
        }

        try {
            $response = $this->client->get("repos/{$ownerAndRepo}");
   
            return json_decode((string) $response->getBody(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function getReleaseData(string $repoUrl): array
    {
        $ownerAndRepo = $this->extractOwnerRepo($repoUrl);

        if ($ownerAndRepo === null) {
            return ['error' => 'Invalid GitHub URL: ' . $repoUrl];
        }

        try {
            $releaseUrl = $this->getReleaseUrl($ownerAndRepo);
            $response = $this->client->get($releaseUrl);
            $releases = json_decode((string) $response->getBody(), true);

            if (empty($releases[0]['published_at'])) {
                return ['error' => 'No releases found'];
            }

            $lastReleaseDate = Carbon::parse($releases[0]['published_at']);

            return [
                'latest_at' => $lastReleaseDate->format('M j, Y'),
                'time_since' => ($lastReleaseDate->diffInMonths() >= 12 ? "⚠️  " : "") . $lastReleaseDate->diffForHumans(),
            ];
        } catch (GuzzleException|\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function extractOwnerRepo(string $url): ?string
    {
        preg_match('#github\.com/([^/]+/[^/.]+)(\.git)?#', $url, $matches);
        
        return $matches[1] ?? null;
    }


    protected function getReleaseUrl(string $ownerAndRepo): string
    {
        list($owner, $repo) = explode('/', $ownerAndRepo);

        return "https://api.github.com/repos/{$owner}/{$repo}/releases";
    }
}
