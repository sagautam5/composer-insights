<?php 

namespace ComposerInsights\Services;

use ComposerInsights\Exporters\CsvExporter;
use ComposerInsights\Exporters\JsonExporter;
use ComposerInsights\Exporters\BaseExporter;
class ExportResolver
{
    public function resolve(string $format, string|null $path = null): BaseExporter
    {
         $exporter = match ($format) {
                'json' => new JsonExporter($path),
                'csv' => new CsvExporter($path),
            };

        return $exporter; 
    }
}