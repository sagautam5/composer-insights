<?php

use ComposerInsights\Services\CacheManager;
use ComposerInsights\Services\DirectoryResolver;
use Carbon\Carbon;

beforeEach(function () {
    $this->cacheKey = 'vendor/package';
    $this->cacheDir = DirectoryResolver::resolve('cache');
    $this->cacheFile = $this->cacheDir . str_replace('/', '-', $this->cacheKey) . '.json';

    if (file_exists($this->cacheFile)) {
        unlink($this->cacheFile);
    }
});

afterEach(function () {
    if (file_exists($this->cacheFile)) {
        unlink($this->cacheFile);
    }

    if(is_dir(__DIR__ . '/../../../.composer-insights/cache')) {
        rmdir(__DIR__ . '/../../../.composer-insights/cache');
        rmdir(__DIR__ . '/../../../.composer-insights/');   
    }
});

test('it saves and loads cache correctly if not expired', function () {
    $manager = new CacheManager();
    $data = ['key' => 'value'];

    $manager->saveToCache($this->cacheKey, $data);

    expect(file_exists($this->cacheFile))->toBeTrue();

    $loaded = $manager->loadFromCache($this->cacheKey);
    expect($loaded)->toMatchArray($data);
});

test('it returns null if cache file does not exist', function () {
    $manager = new CacheManager();

    $result = $manager->loadFromCache('non-existent-key');

    expect($result)->toBeNull();
});

test('it returns null if cache is older than 24 hours', function () {
    $expired = [
        '__cached_at' => Carbon::now()->subHours(25)->toISOString(),
        'data' => ['stale' => true]
    ];

    mkdir($this->cacheDir, 0777, true);
    file_put_contents($this->cacheFile, json_encode($expired));

    $manager = new CacheManager();
    $result = $manager->loadFromCache($this->cacheKey);

    expect($result)->toBeNull();
});

test('it returns cached data if it is within 24 hours', function () {
    $fresh = [
        '__cached_at' => Carbon::now()->toISOString(),
        'data' => ['fresh' => true]
    ];

    mkdir($this->cacheDir, 0777, true);
    file_put_contents($this->cacheFile, json_encode($fresh));

    $manager = new CacheManager();
    $result = $manager->loadFromCache($this->cacheKey);

    expect($result)->toMatchArray(['fresh' => true]);
});
