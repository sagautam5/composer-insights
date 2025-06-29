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
            'no-dev' => $input->getOption('no-dev'),
            'no-summary' => $input->getOption('no-summary'),
            'no-table' => $input->getOption('no-table'),
            'export' => $input->getOption('export'),
        ];
    }
}