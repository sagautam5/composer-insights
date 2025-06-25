<?php

namespace ComposerInsights\Services;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class TableRenderer
{
    public function render(array $rows, OutputInterface $output): void
    {
        $table = new Table($output);
        
        $table->setHeaders($this->getTableHeaders());

        $table->addRows($rows);
        
        $table->render();
    }

    private function getTableHeaders(): array
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
}
