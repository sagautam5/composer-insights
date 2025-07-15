<?php 

namespace ComposerInsights\Exporters;

use ComposerInsights\Services\DirectoryResolver;
use Symfony\Component\Console\Output\OutputInterface;

class JsonExporter extends BaseExporter
{
    public function export(array $insights, OutputInterface $output): void
    {
        $path = DirectoryResolver::resolve('output') . 'data.json';

        DirectoryResolver::createDirectoryIfNotExists($path);

        $json = json_encode($insights, JSON_PRETTY_PRINT);

        file_put_contents($path, $json);

        $output->writeln("<info>JSON exported to:</info> <comment>.composer-insights/output/data.json</comment>\n");
    }
}