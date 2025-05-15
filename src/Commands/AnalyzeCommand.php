<?php

namespace ComposerInsights\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ComposerInsights\GitHub\GitHubAnalyzer;

class AnalyzeCommand extends Command
{
    protected static $defaultName = 'analyze';

    protected function configure(): void
    {
        $this->setDescription('Analyzes the current composer.lock and provides insights.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🔍 Composer Insights Analysis</info>');

        $lockPath = getcwd() . '/composer.lock';
        if (!file_exists($lockPath)) {
            $output->writeln('<error>composer.lock file not found in current directory.</error>');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($lockPath), true);
        $packages = $data['packages'] ?? [];

        $analyzer = new GitHubAnalyzer(getenv('GITHUB_TOKEN'));

        foreach ($packages as $package) {
            $name = $package['name'];
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

            $output->writeln("🔹 {$name}");
            $output->writeln("  ⭐ Stars: {$info['stargazers_count']}");
            $output->writeln("  🍴 Forks: {$info['forks_count']}");
            $output->writeln("  🛠 Open Issues: {$info['open_issues_count']}");
            $output->writeln("  📅 Updated: {$info['updated_at']}");
            $output->writeln('');
        }

        $output->writeln("\n<info>✅ Done</info>");
        return Command::SUCCESS;
    }
}
