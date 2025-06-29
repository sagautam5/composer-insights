<?php 

namespace ComposerInsights\Services;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
class ConfigureAnalyzeCommand
{
    public function setup(Command $command)
    {
        return $command->setDescription('Analyzes the current composer files and provides insights.')
            ->addOption('days', mode: InputOption::VALUE_OPTIONAL, description: 'No of days to look back for health check', default: 180)
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Include dev dependencies')
            ->addOption('no-dev', null, InputOption::VALUE_NONE, 'Exclude dev dependencies')
            ->addOption('export', mode: InputOption::VALUE_REQUIRED, description: 'Output results as JSON', suggestedValues: ['json', 'csv'])
            ->addOption('no-summary', null, InputOption::VALUE_NONE, 'Do not show summary')
            ->addOption('no-table', null, InputOption::VALUE_NONE, 'Do not show table');
    }
}