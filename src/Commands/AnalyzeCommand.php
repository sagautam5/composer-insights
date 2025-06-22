<?php

namespace ComposerInsights\Commands;

use Carbon\Carbon;
use ComposerInsights\Services\GitHubAnalyzer;
use ComposerInsights\Services\ComposerDependencyLoader;
use ComposerInsights\Services\PackagistInsightResolver;
use ComposerInsights\Services\TableRenderer;
use Symfony\Component\Console\Command\Command;
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

        $dependencyLoader = new ComposerDependencyLoader();

        if (!$dependencyLoader->hasComposerFiles()) {
            $output->writeln('<error>composer.lock or composer.json not found.</error>');
            return Command::FAILURE;
        }

        $includeDev = $input->getOption('dev');
        $excludeDev = $input->getOption('no-dev');

        [$explicitRequires, $packages] = $dependencyLoader->loadComposerData($includeDev, $excludeDev);

        $this->renderAnalysisTable($output, $packages, $explicitRequires);

        $output->writeln("\n<info>✅ Done</info>");
        return Command::SUCCESS;
    }

    private function renderAnalysisTable(OutputInterface $output, array $packages, array $explicitRequires): void
    {
        $rows = [];
        foreach ($packages as $package) {
            $row = $this->analyzePackage($package, $explicitRequires, $output);
            
            if ($row !== null) {
                $rows[] = $row;
            }
        }
        
        $renderer = new TableRenderer();
        $renderer->render($rows, $output);
    }

    private function analyzePackage(array $package, array $explicitRequires, OutputInterface $output): ?array
    {
        $analyzer = new GitHubAnalyzer(getenv('GITHUB_TOKEN') ?? null);
        
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

        $info['release_data'] = $analyzer->getReleaseData($repoUrl);
        
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
        $currentVersion = $package['version'] ?? 'N/A';

        $info['downloads'] = $metadata['downloads'] ?? ['total' => 'N/A'];
        
        $license = is_array($license) ? implode(', ', $license) : ($license ?? 'N/A');

        $info['updated_at'] = Carbon::parse($info['updated_at']);

        $info['updated_at'] = $info['updated_at']->diffInDays(Carbon::now()) > 30 * 6 ? "<fg=red>{$info['updated_at']->diffForHumans()}</>" : $info['updated_at']->diffForHumans();
        
        $info['dependents'] = $metadata['dependents'] ?? 'N/A';
        $info['suggesters'] = $metadata['suggesters'] ?? 'N/A';
        $versionInfo = ($latestVersion ?? 'N/A') . " | " . ($package['version'] ?? 'N/A');
         
        $releaseInfo = ($info['release_data']['last_release_date'] ?? 'N/A') . " | " . ($info['release_data']['time_since_last_release'] ?? 'N/A');

        $versionInfo = $latestVersion == $currentVersion ? "<fg=green>{$versionInfo}</>" : "<bg=red>{$versionInfo}</>";

        return [
            $name,
            $license,
            $versionInfo,
            FormatHelper::humanNumber($info['stargazers_count']),
            FormatHelper::humanNumber($info['forks_count']),
            FormatHelper::humanNumber($info['open_issues_count']),
            FormatHelper::humanNumber($info['downloads']['total']),
            $info['updated_at'],
            $releaseInfo,
            FormatHelper::humanNumber($info['dependents']),
            FormatHelper::humanNumber($info['suggesters']),
        ];
    }
}
