<?php

namespace ComposerInsights\Commands;

use ComposerInsights\Services\ComposerDependencyLoader;
use ComposerInsights\Services\ConfigureAnalyzeCommand;
use ComposerInsights\Services\ExportResolver;
use ComposerInsights\Services\InputOptionResolver;
use ComposerInsights\Services\InsightCollector;
use ComposerInsights\Services\ReportSummaryGenerator;
use ComposerInsights\Services\TableRenderer;
use ComposerInsights\Support\Icon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        (new ConfigureAnalyzeCommand)->setup($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>'.Icon::get('search').' Fetching Composer Dependency Insights</info>');

        $dependencyLoader = new ComposerDependencyLoader();

        if (!$dependencyLoader->hasComposerFiles()) {
            $output->writeln('<error>composer.lock or composer.json not found.</error>');
            return Command::FAILURE;
        }

        $inputOptions = (new InputOptionResolver())->resolve($input);
        
        [$explicitRequires, $packages] = $dependencyLoader->loadComposerData($inputOptions['dev'], $inputOptions['no-dev']);
        
        $insights = (new InsightCollector($inputOptions['days']))->collect($output, $packages, $explicitRequires);

        if($inputOptions['export']) {
            (new ExportResolver())->resolve($inputOptions['export'])->export($insights, $output);
        }else{
            if(!$inputOptions['no-table']) {
                (new TableRenderer())->render($insights, $output);
            }
        }
        
        if(!$inputOptions['no-summary']) {
            (new ReportSummaryGenerator())->generate($insights, $output);
        }
        
        return Command::SUCCESS;
    }
}
