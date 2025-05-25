<?php

namespace ComposerInsights\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ComposerInsights\GitHub\GitHubAnalyzer;
use DateTime;
use Symfony\Component\Console\Helper\Table;

class AnalyzeCommand extends Command
{
    protected static $defaultName = 'analyze';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Analyzes the current composer.lock and provides insights.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🔍 Composer Insights Analysis</info>');

        $basePath = getcwd();
        $lockPath = $basePath . '/composer.lock';
        $jsonPath = $basePath . '/composer.json';

        if (!file_exists($lockPath) || !file_exists($jsonPath)) {
            $output->writeln('<error>composer.lock or composer.json not found.</error>');
            return Command::FAILURE;
        }

        $lockData = json_decode(file_get_contents($lockPath), true);
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        $explicitRequires = array_merge(
            array_keys($jsonData['require'] ?? []),
            array_keys($jsonData['require-dev'] ?? [])
        );

        $explicitRequires = array_map('strtolower', $explicitRequires); // normalize names

        $packages = array_merge($lockData['packages'] ?? [], $lockData['packages-dev'] ?? []);
        $analyzer = new GitHubAnalyzer($_ENV['GITHUB_TOKEN']);

        $table = new Table($output);
        $table->setHeaders(['Package', 'Stars', 'Forks', 'Open Issues', 'Last Updated', 'Downloads']);

        foreach ($packages as $package) {
            $name = strtolower($package['name']);
            if (!in_array($name, $explicitRequires)) {
                continue; // skip transitive dependencies
            }

            $repo = $package['source']['url'] ?? null;
            if (!$repo || !str_contains($repo, 'github.com')) {
                $output->writeln("[SKIP] {$name} (non-GitHub)");
                continue;
            }

            $info = $analyzer->fetchRepoData($repo);
            if (isset($info['error'])) {
                $output->writeln("[ERROR] {$name}: {$info['error']}");
                continue;
            }

            $packageParts = explode('/', $name);
            $downloads = $this->fetchPackagistDownloads($packageParts[0], $packageParts[1]);
            $info['downloads'] = $downloads ?? ['total' => 'N/A'];

            $info['updated_at'] = $this->timeAgo($info['updated_at']);

            $table->addRow([
                $name,
                $this->humanNumber($info['stargazers_count']),
                $this->humanNumber($info['forks_count']),
                $this->humanNumber($info['open_issues_count']),
                $info['updated_at'],
                $this->humanNumber($info['downloads']['total']),
            ]);
        }

        $table->render();

        $output->writeln("\n<info>✅ Done</info>");
        return Command::SUCCESS;
    }


    protected function timeAgo($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);  // convert days to weeks
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    protected function fetchPackagistDownloads(string $vendor, string $package): ?array
    {
        $url = "https://packagist.org/packages/{$vendor}/{$package}.json";

        $context = stream_context_create([
            'http' => ['header' => 'User-Agent: PHP']
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!$json) return null;

        $data = json_decode($json, true);

        return $data['package']['downloads'] ?? null;
    }

    protected function humanNumber(int $number): string
    {
        if ($number >= 1_000_000_000) {
            return round($number / 1_000_000_000, 1) . 'B';
        }

        if ($number >= 1_000_000) {
            return round($number / 1_000_000, 1) . 'M';
        }

        if ($number >= 1_000) {
            return round($number / 1_000, 1) . 'k';
        }

        return (string) $number;
    }

}
