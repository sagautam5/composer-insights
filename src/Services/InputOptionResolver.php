<?php 

namespace ComposerInsights\Services;

use Symfony\Component\Console\Input\InputInterface;

class InputOptionResolver
{
    public function resolve(InputInterface $input): array
    {
        return [
            'days' => $input->getOption('days'),
            'dev' => $input->getOption('dev'),
            'prod' => $input->getOption('prod'),
            'no-summary' => $input->getOption('no-summary'),
            'no-table' => $input->getOption('no-table'),
            'export' => $input->getOption('export'),
            'no-cache' => $input->getOption('no-cache'),
            'export-path' => $input->getOption('export-path'),
        ];
    }
}