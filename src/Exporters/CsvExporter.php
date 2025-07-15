<?php 

namespace ComposerInsights\Exporters;

use ComposerInsights\Services\DirectoryResolver;
use ComposerInsights\Support\PackageInsight;
use Symfony\Component\Console\Output\OutputInterface;

class CsvExporter extends BaseExporter
{
    public function export(array $insights, OutputInterface $output): void
    {
        $path = DirectoryResolver::resolve('output') . 'data.csv';
        DirectoryResolver::createDirectoryIfNotExists($path);

        $handle = fopen($path, 'w');
        
        $headers = (new PackageInsight($insights[0]))->headers();

        fputcsv($handle, $headers);

        foreach ($insights as $insight) {
            $rowToBeWritten = [];
            foreach ($insight as $items) {
                foreach ($items as $key =>$item) {
                    $rowToBeWritten[$key] = $item;
                }

            }
            fputcsv($handle, $rowToBeWritten);
        }

        fclose($handle);

        $output->writeln("<info>CSV exported to:</info> <comment>.composer-insights/output/data.csv</comment>\n");
    }
}