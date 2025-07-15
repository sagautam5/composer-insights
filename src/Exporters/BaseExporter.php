<?php 

namespace ComposerInsights\Exporters;
use Symfony\Component\Console\Output\OutputInterface;
abstract class BaseExporter
{
    public function __construct(){}

    abstract public function export(array $insights, OutputInterface $output);
}