<?php

use ComposerInsights\Services\ExportResolver;
use ComposerInsights\Exporters\JsonExporter;
use ComposerInsights\Exporters\CsvExporter;
use ComposerInsights\Exporters\BaseExporter;

test('it resolves json exporter', function () {
    $resolver = new ExportResolver();
    $exporter = $resolver->resolve('json');

    expect($exporter)->toBeInstanceOf(JsonExporter::class)
                    ->toBeInstanceOf(BaseExporter::class);
});

test('it resolves csv exporter', function () {
    $resolver = new ExportResolver();
    $exporter = $resolver->resolve('csv');

    expect($exporter)->toBeInstanceOf(CsvExporter::class)
                    ->toBeInstanceOf(BaseExporter::class);
});

test('it throws error for unsupported format', function () {
    $resolver = new ExportResolver();

    expect(fn () => $resolver->resolve('xml'))
        ->toThrow(Error::class);
});
