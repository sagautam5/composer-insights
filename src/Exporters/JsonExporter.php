<?php 

namespace ComposerInsights\Exporters;

use Symfony\Component\Console\Output\OutputInterface;

class JsonExporter extends BaseExporter
{
    public function export(array $insights, OutputInterface $output): void
    {
        $path = $this->resolveOutputPath('json');

        $json = json_encode($insights, JSON_PRETTY_PRINT);

        file_put_contents($path, $json);

        $output->writeln("<info>JSON exported to:</info> <comment>".realpath($path)."</comment>\n");
    }
}