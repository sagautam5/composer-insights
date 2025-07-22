<?php 

namespace ComposerInsights\Exporters;

use ComposerInsights\Services\DirectoryResolver;
use Symfony\Component\Console\Output\OutputInterface;
abstract class BaseExporter
{
    public function __construct(protected string|null $path){}

    abstract public function export(array $insights, OutputInterface $output);

    protected function resolveOutputPath(string $extension): string
    {
        $path = DirectoryResolver::resolve('output') . 'data.'.$extension;

        DirectoryResolver::createDirectoryIfNotExists($path);

        return $path;
    }
}