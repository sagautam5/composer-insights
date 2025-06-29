<?php 

namespace ComposerInsights\Services;

use ComposerInsights\Exporters\CsvExporter;
use ComposerInsights\Exporters\JsonExporter;
use ComposerInsights\Exporters\BaseExporter;
class ExportResolver
{
    public function resolve(string $format): BaseExporter
    {
         $exporter = match ($format) {
                'json' => new JsonExporter(),
                'csv' => new CsvExporter(),
            };

        return $exporter; 
    }
}