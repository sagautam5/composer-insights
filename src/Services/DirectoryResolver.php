<?php 

namespace ComposerInsights\Services;

class DirectoryResolver
{
    public static function resolve(string $path): string
    {
        if(is_dir(__DIR__ . '/../../vendor/')) {
            return __DIR__ . "/../../.composer-insights/{$path}/";
        }

        return __DIR__ . "/../../../../../.composer-insights/{$path}/";
    }

    public static function createDirectoryIfNotExists($path): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }
}