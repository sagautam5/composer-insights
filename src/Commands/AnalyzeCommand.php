<?php

namespace ComposerInsights\Commands;

use Carbon\Carbon;
use ComposerInsights\Services\GitHubAnalyzer;
use ComposerInsights\Services\ComposerDependencyLoader;
use ComposerInsights\Services\InsightCollector;
use ComposerInsights\Services\PackagistInsightResolver;
use ComposerInsights\Services\TableRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ComposerInsights\Support\NumberFormatter;
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
        
        $insights = (new InsightCollector())->collect($output, $packages, $explicitRequires);

        $renderer = new TableRenderer();
        
        $renderer->render($insights, $output);
        
        $output->writeln("\n<info>✅ Done</info>");
        return Command::SUCCESS;
    }
}
