<?php

use ComposerInsights\Support\FormatHelper;

test('it formats single digits', function () {
    expect(FormatHelper::humanNumber(5))->toBe('5');
});

test('it formats double digits', function () {
    expect(FormatHelper::humanNumber(42))->toBe('42');
});

test('it formats hundreds', function () {
    expect(FormatHelper::humanNumber(500))->toBe('500');
});

test('it formats thousands', function () {
    expect(FormatHelper::humanNumber(1500))->toBe('1.5k');
});

test('it formats tens of thousands', function () {
    expect(FormatHelper::humanNumber(15000))->toBe('15k');
});

test('it formats hundreds of thousands', function () {
    expect(FormatHelper::humanNumber(150000))->toBe('150k');
});

test('it formats millions', function () {
    expect(FormatHelper::humanNumber(1500000))->toBe('1.5M');
});

test('it formats tens of millions', function () {
    expect(FormatHelper::humanNumber(15000000))->toBe('15M');
});

test('it formats hundreds of millions', function () {
    expect(FormatHelper::humanNumber(150000000))->toBe('150M');
});

test('it formats billions', function () {
    expect(FormatHelper::humanNumber(1500000000))->toBe('1.5B');
});

test('it handles non-numeric strings', function () {
    expect(FormatHelper::humanNumber('N/A'))->toBe('N/A');
});

test('it handles numeric strings', function () {
    expect(FormatHelper::humanNumber('1200'))->toBe('1.2k');
});

test('it returns "just now" for current time', function () {
    $now = (new DateTime())->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($now))->toBe('just now');
});

test('it returns "1 second ago"', function () {
    $dt = (new DateTime())->modify('-1 second')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt))->toBe('1 second ago');
});

test('it returns "2 seconds ago"', function () {
    $dt = (new DateTime())->modify('-2 seconds')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt))->toBe('2 seconds ago');
});

test('it returns "1 minute ago"', function () {
    $dt = (new DateTime())->modify('-1 minute')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt))->toBe('1 minute ago');
});

test('it returns "2 minutes ago"', function () {
    $dt = (new DateTime())->modify('-2 minutes')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt))->toBe('2 minutes ago');
});

test('it returns "1 hour ago"', function () {
    $dt = (new DateTime())->modify('-1 hour')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt))->toBe('1 hour ago');
});

test('it returns "1 day ago"', function () {
    $dt = (new DateTime())->modify('-1 day')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt))->toBe('1 day ago');
});

test('it returns "1 week ago"', function () {
    $dt = (new DateTime())->modify('-7 days')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt))->toBe('1 week ago');
});

test('returns "1 year, 2 months, 3 weeks ago" with full = true', function () {
    $dt = (new DateTime())->modify('-1 year -2 months -21 days')->format('Y-m-d H:i:s');
    expect(FormatHelper::timeAgo($dt, true))->toBe('1 year, 2 months, 3 weeks ago');
});
