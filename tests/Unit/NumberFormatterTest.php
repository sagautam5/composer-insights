<?php

use ComposerInsights\Support\NumberFormatter;

test('it formats null values', function () {
    expect(NumberFormatter::humanize(null))->toBe('N/A');
});

test('it formats single digits', function () {
    expect(NumberFormatter::humanize(5))->toBe('5');
});

test('it formats double digits', function () {
    expect(NumberFormatter::humanize(42))->toBe('42');
});

test('it formats hundreds', function () {
    expect(NumberFormatter::humanize(500))->toBe('500');
});

test('it formats thousands', function () {
    expect(NumberFormatter::humanize(1500))->toBe('1.5 k');
});

test('it formats tens of thousands', function () {
    expect(NumberFormatter::humanize(15000))->toBe('15 k');
});

test('it formats hundreds of thousands', function () {
    expect(NumberFormatter::humanize(150000))->toBe('150 k');
});

test('it formats millions', function () {
    expect(NumberFormatter::humanize(1500000))->toBe('1.5 M');
});

test('it formats tens of millions', function () {
    expect(NumberFormatter::humanize(15000000))->toBe('15 M');
});

test('it formats hundreds of millions', function () {
    expect(NumberFormatter::humanize(150000000))->toBe('150 M');
});

test('it formats billions', function () {
    expect(NumberFormatter::humanize(1500000000))->toBe('1.5 B');
});

test('it handles non-numeric strings', function () {
    expect(NumberFormatter::humanize('N/A'))->toBe('N/A');
});

test('it handles numeric strings', function () {
    expect(NumberFormatter::humanize('1200'))->toBe('1.2 k');
});
