<?php

use ComposerInsights\Support\Icon;

it('returns correct icon for valid keys', function () {
    expect(Icon::get('bug'))->toBe('🐞');
    expect(Icon::get('done'))->toBe('✅');
    expect(Icon::get('download'))->toBe('⬇️');
    expect(Icon::get('report'))->toBe('📊');
    expect(Icon::get('search'))->toBe('🔍');
    expect(Icon::get('star'))->toBe('⭐');
    expect(Icon::get('warning'))->toBe('⚠️');
    expect(Icon::get('arrow'))->toBe('➡️');
    expect(Icon::get('downtrend'))->toBe('📉');
    expect(Icon::get('health'))->toBe('🧪');
    expect(Icon::get('major'))->toBe('🔴');
    expect(Icon::get('minor'))->toBe('🟡');
    expect(Icon::get('patch'))->toBe('🟣');
    expect(Icon::get('package'))->toBe('📦');
    expect(Icon::get('waiting'))->toBe('⏳');
});

it('returns empty string for unknown icon key', function () {
    expect(Icon::get('nonexistent'))->toBe('');
});

it('is case sensitive', function () {
    expect(Icon::get('Bug'))->toBe('');
});

it('contains all expected keys', function () {
    $expectedKeys = [
        'arrow', 'bug', 'done', 'download', 'downtrend', 'health',
        'major', 'minor', 'patch', 'package', 'report', 'search',
        'star', 'warning', 'waiting'
    ];

    foreach ($expectedKeys as $key) {
        expect(Icon::get($key))->not()->toBe('');
    }
});
