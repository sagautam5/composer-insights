<?php

namespace ComposerInsights\Commands;

use ComposerInsights\GitHub\GitHubAnalyzer;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCommand extends Command
{
    protected static $defaultName = 'analyze';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Analyzes the current composer files and provides insights.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🔍 Fetching Composer Dependency Insights</info>');

        if (!$this->hasComposerFiles()) {
            $output->writeln('<error>composer.lock or composer.json not found.</error>');
            return Command::FAILURE;
        }

        [$explicitRequires, $packages] = $this->loadComposerData();
        $analyzer = new GitHubAnalyzer($_ENV['GITHUB_TOKEN'] ?? null);

        $this->renderAnalysisTable($output, $packages, $explicitRequires, $analyzer);

        $output->writeln("\n<info>✅ Done</info>");
        return Command::SUCCESS;
    }

    private function hasComposerFiles(): bool
    {
        return file_exists('composer.lock') && file_exists('composer.json');
    }

    private function loadComposerData(): array
    {
        $lock = json_decode(file_get_contents('composer.lock'), true);
        $json = json_decode(file_get_contents('composer.json'), true);

        $requires = array_merge(
            array_keys($json['require'] ?? []),
            array_keys($json['require-dev'] ?? [])
        );

        $requires = array_map('strtolower', $requires);
        $packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);

        return [$requires, $packages];
    }

    private function renderAnalysisTable(OutputInterface $output, array $packages, array $explicitRequires, GitHubAnalyzer $analyzer): void
    {
        $table = new Table($output);
        $table->setHeaders(['Package', 'Stars', 'Forks', 'Open Issues', 'Last Updated', 'Downloads']);

        foreach ($packages as $package) {
            $row = $this->analyzePackage($package, $explicitRequires, $analyzer, $output);
            if ($row !== null) {
                $table->addRow($row);
            }
        }

        $table->render();
    }

    private function analyzePackage(array $package, array $explicitRequires, GitHubAnalyzer $analyzer, OutputInterface $output): ?array
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

        $info = $analyzer->fetchRepoData($repoUrl);
        if (isset($info['error'])) {
            $output->writeln("[ERROR] {$name}: {$info['error']}");
            return null;
        }

        $downloads = $this->fetchPackagistDownloads(...explode('/', $name));
        $info['downloads'] = $downloads ?? ['total' => 'N/A'];
        $info['updated_at'] = $this->timeAgo($info['updated_at']);

        return [
            $name,
            $this->humanNumber($info['stargazers_count']),
            $this->humanNumber($info['forks_count']),
            $this->humanNumber($info['open_issues_count']),
            $info['updated_at'],
            $this->humanNumber($info['downloads']['total']),
        ];
    }

    private function fetchPackagistDownloads(string $vendor, string $package): ?array
    {
        $url = "https://packagist.org/packages/{$vendor}/{$package}.json";
        $context = stream_context_create([
            'http' => ['header' => 'User-Agent: ComposerInsights']
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        return $data['package']['downloads'] ?? null;
    }

    private function humanNumber(int|string $number): string
    {
        if (!is_numeric($number)) return (string) $number;

        $number = (int) $number;

        return match (true) {
            $number >= 1_000_000_000 => round($number / 1_000_000_000, 1) . 'B',
            $number >= 1_000_000     => round($number / 1_000_000, 1) . 'M',
            $number >= 1_000         => round($number / 1_000, 1) . 'k',
            default                  => (string) $number,
        };
    }

    private function timeAgo(string $datetime, bool $full = false): string
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = (int) floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $units = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        $parts = [];
        foreach ($units as $key => $label) {
            if ($diff->$key) {
                $parts[] = $diff->$key . ' ' . $label . ($diff->$key > 1 ? 's' : '');
            }
        }

        return $parts ? implode(', ', $full ? $parts : [reset($parts)]) . ' ago' : 'just now';
    }
}
