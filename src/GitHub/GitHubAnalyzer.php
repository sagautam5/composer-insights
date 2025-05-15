<?php

namespace ComposerInsights\GitHub;

use GuzzleHttp\Client;

class GitHubAnalyzer
{
    protected Client $client;

    public function __construct(?string $githubToken)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.github.com/',
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'Authorization' => $githubToken ? "token $githubToken" : null,
            ],
        ]);
    }

    public function fetchRepoData(string $repoUrl): array
    {
        // Extract `owner/repo` from GitHub URL (with optional .git)
        preg_match('#github\\.com/([^/]+/[^/.]+)(\\.git)?#', $repoUrl, $matches);

        if (!isset($matches[1])) {
            return ['error' => 'Invalid GitHub URL: ' . $repoUrl];
        }

        $ownerRepo = $matches[1];

        try {
            $response = $this->client->get("repos/{$ownerRepo}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

}
