<?php 

namespace ComposerInsights\Services;

class ComposerDependencyLoader
{
    public function hasComposerFiles(): bool
    {
        return file_exists('composer.json') && file_exists('composer.lock');
    }
    
    public function loadComposerData(bool $includeDev, bool $excludeDev): array
    {
        if ($includeDev && $excludeDev) {
            throw new \InvalidArgumentException('Cannot include and exclude dev dependencies at the same time.');
        }

        $json = json_decode(file_get_contents('composer.json'), true);
        $lock = json_decode(file_get_contents('composer.lock'), true);

        $requires = match (true) {
            $excludeDev => array_keys($json['require'] ?? []),
            $includeDev => array_keys($json['require-dev'] ?? []),
            default => array_merge(
                array_keys($json['require'] ?? []),
                array_keys($json['require-dev'] ?? [])
            ),
        };

        $requires = array_map('strtolower', $requires);
        $packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);

        return [$requires, $packages];
    }
}