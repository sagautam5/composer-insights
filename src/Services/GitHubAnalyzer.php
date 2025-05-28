<?php

namespace ComposerInsights\Services;

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
        $ownerRepo = $this->extractOwnerRepo($repoUrl);

        if ($ownerRepo === null) {
            return ['error' => 'Invalid GitHub URL: ' . $repoUrl];
        }

        try {
            $response = $this->client->get("repos/{$ownerRepo}");
            return json_decode((string) $response->getBody(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function extractOwnerRepo(string $url): ?string
    {
        preg_match('#github\.com/([^/]+/[^/.]+)(\.git)?#', $url, $matches);
        return $matches[1] ?? null;
    }
}
