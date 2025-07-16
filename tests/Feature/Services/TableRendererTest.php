<?php

use ComposerInsights\Services\TableRenderer;

beforeEach(function () {
    $this->renderer = new TableRenderer();
});

test('it returns the expected table headers', function () {
    $headers = $this->renderer->getTableHeaders();

    expect($headers)->toBe([
        'Package',
        'License',
        'Latest Version',
        'Used Version',
        'Last Updated',
        'Last Release (Date | Time)',
        'Downloads',
        'Stars',
        'Forks',
        'Open Issues',
        'Dependents',
        'Suggesters',
    ]);
});

test('it formats insights via PackageInsight to array', function () {
    $input = [[
        'package' => ['name' => 'demo/package', 'license' => 'Apache-2.0'],
        'version' => ['latest' => '1.2.3', 'used' => '1.0.0', 'is_outdated' => false],
        'maintenance' => ['updated_at' => '2023-12-01', 'is_stale' => false],
        'release' => ['latest_at' => '2023-11-01', 'time_since' => '2 months ago', 'no_recent_release' => false],
        'popularity' => ['downloads' => 1200000, 'stars' => 1650, 'forks' => 12],
        'health' => ['open_issues' => 4, 'dependents' => 2, 'suggesters' => 0],
    ]];

    $result = $this->renderer->formatInsights($input);
    
    expect($result)->toBeArray();
    expect($result[0]['package'])->toBe('demo/package');
    expect($result[0]['license'])->toBe('Apache-2.0');
    expect($result[0]['latestVersion'])->toBe('1.2.3');
    expect($result[0]['downloads'])->toBe('1.2 M');
    expect($result[0]['stars'])->toBe('1.7 k');
    expect($result[0]['forks'])->toBe('12');
});


