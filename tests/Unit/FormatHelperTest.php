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
