<?php 

namespace ComposerInsights\Exporters;

use ComposerInsights\Support\PackageInsight;
use Symfony\Component\Console\Output\OutputInterface;

class CsvExporter extends BaseExporter
{
    public function export(array $insights, OutputInterface $output): void
    {
        $path = $this->getOutputPath() . 'data.csv';
        $this->createDirectoryIfNotExists($path);

        $handle = fopen($path, 'w');
        
        $headers = (new PackageInsight($insights[0]))->headers();

        fputcsv($handle, $headers);

        foreach ($insights as $row) {
            $rowValues = [];
            foreach ($row as $category => $items) {
                foreach ($items as $key =>$item) {
                    $rowValues[$key] = $item;
                }

            }
            fputcsv($handle, $rowValues);
        }

        fclose($handle);

        $output->writeln("<info>CSV exported to:</info> <comment>./output/data.csv</comment>\n");
    }
}