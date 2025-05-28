<?php

namespace ComposerInsights\Commands;

use ComposerInsights\Services\GitHubAnalyzer;
use ComposerInsights\Services\ComposerDependencyLoader;
use ComposerInsights\Services\PackagistInsightResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ComposerInsights\Support\FormatHelper;
use Symfony\Component\Console\Input\InputOption;

class AnalyzeCommand extends Command
{
    protected static $defaultName = 'analyze';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Analyzes the current composer files and provides insights.')
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Include dev dependencies')
            ->addOption('no-dev', null, InputOption::VALUE_NONE, 'Exclude dev dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🔍 Fetching Composer Dependency Insights</info>');

        if (!$this->hasComposerFiles()) {
            $output->writeln('<error>composer.lock or composer.json not found.</error>');
            return Command::FAILURE;
        }

        $includeDev = $input->getOption('dev');
        $excludeDev = $input->getOption('no-dev');

        [$explicitRequires, $packages] = (new ComposerDependencyLoader)->loadComposerData($includeDev, $excludeDev);
        $analyzer = new GitHubAnalyzer($_ENV['GITHUB_TOKEN'] ?? null);

        $this->renderAnalysisTable($output, $packages, $explicitRequires, $analyzer);

        $output->writeln("\n<info>✅ Done</info>");
        return Command::SUCCESS;
    }

    private function hasComposerFiles(): bool
    {
        return file_exists('composer.lock') && file_exists('composer.json');
    }

    private function renderAnalysisTable(OutputInterface $output, array $packages, array $explicitRequires, GitHubAnalyzer $analyzer): void
    {
        $table = new Table($output);
        $table->setHeaders($this->getTableHeaders());

        foreach ($packages as $package) {
            $row = $this->analyzePackage($package, $explicitRequires, $analyzer, $output);
            if ($row !== null) {
                $table->addRow($row);
            }
        }

        $table->render();
    }

    private function getTableHeaders(): array
    {
        return [
            'Package',
            'License',
            'Latest Version',
            'Current Version',
            'Stars',
            'Forks',
            'Downloads',
            'Open Issues',
            'Last Updated'
        ];
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

        $packagistResolver = new PackagistInsightResolver();
        $metadata = $packagistResolver->fetchMetaData(...explode('/', $name));
        
        $license = $metadata['license'] ?? null;

        if (!$license && isset($metadata['versions'])) {
            foreach ($metadata['versions'] as $ver) {
                if (!empty($ver['license'])) {
                    $license = $ver['license'];
                    break;
                }
            }
        }
        
        $latestVersion = $packagistResolver->fetchLatestVersion(...explode('/', $name));

        $info['downloads'] = $metadata['downloads'] ?? ['total' => 'N/A'];
        
        $license = is_array($license) ? implode(', ', $license) : ($license ?? 'N/A');

        $info['updated_at'] = FormatHelper::timeAgo($info['updated_at']);

        return [
            $name,
            $license,
            $latestVersion ?? 'N/A',
            $package['version'] ?? 'N/A',
            FormatHelper::humanNumber($info['stargazers_count']),
            FormatHelper::humanNumber($info['forks_count']),
            FormatHelper::humanNumber($info['downloads']['total']),
            FormatHelper::humanNumber($info['open_issues_count']),
            $info['updated_at'],
        ];
    }
}
