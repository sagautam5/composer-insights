<?php

use ComposerInsights\Support\PackageInsight;

it('initializes all public properties correctly', function () {
    $data = [
        'package' => [
            'name' => 'vendor/package',
            'license' => 'MIT',
        ],
        'version' => [
            'latest' => '1.2.3',
            'used' => '1.0.0',
            'is_outdated' => false,
        ],
        'maintenance' => [
            'updated_at' => '2 weeks ago',
            'is_stale' => false,
        ],
        'release' => [
            'latest_at' => '2025-04-01',
            'time_since' => '2 months ago',
            'no_recent_release' => false,
        ],
        'popularity' => [
            'downloads' => 123456,
            'stars' => 5678,
            'forks' => 123,
        ],
        'health' => [
            'open_issues' => 3,
            'dependents' => 456,
            'suggesters' => 78,
        ],
    ];

    $insight = new PackageInsight($data);

    expect($insight->package)->toBe('vendor/package')
        ->and($insight->license)->toBe('MIT')
        ->and($insight->latestVersion)->toBe('1.2.3')
        ->and($insight->usedVersion)->toBe('1.0.0')
        ->and($insight->updatedAt)->toBe('2 weeks ago')
        ->and($insight->latestRelease)->toBe('2025-04-01 | 2 months ago')
        ->and($insight->downloads)->toBe('123.5 k')   // depends on how NumberFormatter is mocked
        ->and($insight->stars)->toBe('5.7 k')
        ->and($insight->forks)->toBe('123')
        ->and($insight->openIssues)->toBe('3')
        ->and($insight->dependents)->toBe('456')
        ->and($insight->suggesters)->toBe('78');
});

it('converts the object to an array correctly', function () {
    $data = [
        'package' => [
            'name' => 'sample/pkg', 
            'license' => 'Apache-2.0'
        ],
        'version' => [
            'latest' => '2.0.0', 
            'used' => '1.8.0', 
            'is_outdated' => false
        ],
        'maintenance' => [
            'updated_at' => 'yesterday', 
            'is_stale' => false
        ],
        'release' => [
            'latest_at' => '2025-05-01', 
            'time_since' => '1 month ago', 
            'no_recent_release' => false
        ],
        'popularity' => [
            'downloads' => 1_000_000, 
            'stars' => 1000, 
            'forks' => 50
        ],
        'health' => [
            'open_issues' => 5, 
            'dependents' => 200, 
            'suggesters' => 10
        ],
    ];

    $insight = new PackageInsight($data);

    expect($insight->toArray())->toMatchArray([
        'package' => 'sample/pkg',
        'license' => 'Apache-2.0',
        'latestVersion' => '2.0.0',
        'usedVersion' => '1.8.0',
        'updatedAt' => 'yesterday',
        'latestRelease' => '2025-05-01 | 1 month ago',
        'downloads' => '1 M',
        'stars' => '1 k',
        'forks' => '50',
        'openIssues' => '5',
        'dependents' => '200',
        'suggesters' => '10',
    ]);
});

it('gracefully handles non-numeric values in popularity or health fields', function () {
    $data = [
        'package' => [
            'name' => 'pkg/example', 
            'license' => 'BSD'
        ],
        'version' => [
            'latest' => '1.0.1', 
            'used' => '1.0.1', 
            'is_outdated' => false
        ],
        'maintenance' => [
            'updated_at' => '3 days ago',
            'is_stale' => false
        ],
        'release' => [
            'latest_at' => '2025-06-01',
            'time_since' => 'just now',
            'no_recent_release' => false
        ],
        'popularity' => [
            'downloads' => 'N/A',
            'stars' => null,
            'forks' => null
        ],
        'health' => [
            'open_issues' => false, 
            'dependents' => null, 
            'suggesters' => 0
        ],
    ];

    $insight = new PackageInsight($data);

    expect($insight->downloads)->toBe('N/A')
        ->and($insight->stars)->toBe('N/A')
        ->and($insight->forks)->toBe('N/A')
        ->and($insight->openIssues)->toBe('0')
        ->and($insight->dependents)->toBe('N/A')
        ->and($insight->suggesters)->toBe('0');
});

it('test headers', function(){
    $data = [
        'package' => [
            'name' => 'pkg/example', 
            'license' => 'BSD'
        ],
        'version' => [
            'latest' => '1.0.1', 
            'used' => '1.0.1', 
            'is_outdated' => false
        ],
        'maintenance' => [
            'updated_at' => '3 days ago', 
            'is_stale' => false
        ],
        'release' => [
            'latest_at' => '2025-06-01', 
            'time_since' => 'just now', 
            'no_recent_release' => false
        ],
        'popularity' => [
            'downloads' => 'N/A', 
            'stars' => null, 
            'forks' => null
        ],
        'health' => [
            'open_issues' => false, 
            'dependents' => null, 
            'suggesters' => 0
        ],
    ];

    $insight = new PackageInsight($data);
    $headers = $insight->headers();

    expect($headers)->not->toBeEmpty();
    expect($headers)->toHaveCount(15);
    
    expect($headers)->toContain('package_name');
    expect($headers)->toContain('package_license');
    expect($headers)->toContain('version_latest');
    expect($headers)->toContain('version_used');
    expect($headers)->toContain('version_is_outdated');
    expect($headers)->toContain('maintenance_updated_at');
    expect($headers)->toContain('maintenance_is_stale');
    expect($headers)->toContain('release_latest_at');
    expect($headers)->toContain('release_time_since');
    expect($headers)->toContain('popularity_downloads');
    expect($headers)->toContain('popularity_stars');
    expect($headers)->toContain('popularity_forks');
    expect($headers)->toContain('health_open_issues');
    expect($headers)->toContain('health_dependents');
    expect($headers)->toContain('health_suggesters');
});