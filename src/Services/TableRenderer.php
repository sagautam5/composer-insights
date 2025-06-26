<?php

namespace ComposerInsights\Services;

use ComposerInsights\Support\PackageInsight;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class TableRenderer
{
    public function render(array $insights, OutputInterface $output): void
    {
        $table = new Table($output);
        
        $table->setHeaders($this->getTableHeaders());

        $rows = $this->formatInsights($insights);

        $table->addRows($rows);
        
        $table->render();

        $output->writeln("\n<info>✅ Done</info>");
    }

    public function getTableHeaders(): array
    {
        return [
            'Package',
            'License',
            'Latest Version',
            'Used Version',
            'Last Updated',
            'Last Release (Date | Time)',
            'Downloads',
            'Stars', 
            'Forks',
            'Open Issues',
            'Dependents',
            'Suggesters',
        ];
    }

    public function formatInsights(array $insights): array
    {
        return array_map(function ($data) {
            return (new PackageInsight($data))->toArray();    
        }, $insights);
    }
}
