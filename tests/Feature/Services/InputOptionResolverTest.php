<?php

use ComposerInsights\Services\InputOptionResolver;
use Symfony\Component\Console\Input\InputInterface;

test('it correctly resolves all input options from InputInterface', function () {

    $input = \Mockery::mock(InputInterface::class);

    $input->shouldReceive('getOption')->with('days')->andReturn(30);
    $input->shouldReceive('getOption')->with('dev')->andReturn(true);
    $input->shouldReceive('getOption')->with('prod')->andReturn(false);
    $input->shouldReceive('getOption')->with('no-summary')->andReturn(true);
    $input->shouldReceive('getOption')->with('no-table')->andReturn(false);
    $input->shouldReceive('getOption')->with('export')->andReturn('json');
    $input->shouldReceive('getOption')->with('no-cache')->andReturn(true);
    $input->shouldReceive('getOption')->with('export-path')->andReturn(null);

    $resolver = new InputOptionResolver();

    $result = $resolver->resolve($input);

    expect($result)->toMatchArray([
        'days' => 30,
        'dev' => true,
        'prod' => false,
        'no-summary' => true,
        'no-table' => false,
        'export' => 'json',
        'no-cache' => true,
        'export-path' => null
    ]);
});

afterEach(function () {
    Mockery::close();
});
