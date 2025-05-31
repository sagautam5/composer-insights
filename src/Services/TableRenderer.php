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

        foreach ($rows as $row) {
            $table->addRow($row);
        }
        
        $table->render();
    }

    private function getTableHeaders(): array
    {
        return [
            'Package',
            'License',
            'Version (Latest|Used)',
            'Stars | Forks | Open Issues',
            'Downloads',
            'Last Updated',
            'Last Release (Date | Time)',
        ];
    }
}
