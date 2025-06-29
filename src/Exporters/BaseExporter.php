<?php 

namespace ComposerInsights\Exporters;
use Symfony\Component\Console\Output\OutputInterface;
abstract class BaseExporter
{
    public function __construct()
    {
    }

    abstract public function export(array $insights, OutputInterface $output);

    protected function getOutputPath(): string
    {
        if(is_dir(__DIR__ . '/../../vendor/')) {
            return __DIR__ . '/../../output/';
        }

        return __DIR__ . '/../../../../../output/';
    }

    protected function createDirectoryIfNotExists($path)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }
}