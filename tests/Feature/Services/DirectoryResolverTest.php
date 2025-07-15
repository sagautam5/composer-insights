<?php

use ComposerInsights\Services\DirectoryResolver;

beforeEach(function () {
    $this->tempBaseDir = sys_get_temp_dir() . '/composer-resolver-test';
    $this->testPath = $this->tempBaseDir . '/subdir/test.json';

    if (is_dir(dirname($this->testPath))) {
        array_map('unlink', glob(dirname($this->testPath) . '/*'));
        rmdir(dirname($this->testPath));
    }

    if (is_dir($this->tempBaseDir)) {
        rmdir($this->tempBaseDir);
    }
});

afterEach(function () {
    if (file_exists($this->testPath)) {
        unlink($this->testPath);
    }

    if (is_dir(dirname($this->testPath))) {
        rmdir(dirname($this->testPath));
    }

    if (is_dir($this->tempBaseDir)) {
        rmdir($this->tempBaseDir);
    }
});

test('it resolves the correct directory path depending on environment', function () {
    $resolvedPath = DirectoryResolver::resolve('cache');

    expect($resolvedPath)
        ->toBeString()
        ->toContain('/.composer-insights/cache/')
        ->and(str_ends_with($resolvedPath, '/'));
});

test('it creates the parent directory if it does not exist', function () {
    expect(is_dir(dirname($this->testPath)))->toBeFalse();

    DirectoryResolver::createDirectoryIfNotExists($this->testPath);

    expect(is_dir(dirname($this->testPath)))->toBeTrue();
});

test('it does not fail if the directory already exists', function () {
    mkdir(dirname($this->testPath), 0777, true);

    expect(is_dir(dirname($this->testPath)))->toBeTrue();

    DirectoryResolver::createDirectoryIfNotExists($this->testPath);

    expect(is_dir(dirname($this->testPath)))->toBeTrue();
});
