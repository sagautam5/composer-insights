<?php 

namespace ComposerInsights\Support;

class Icon
{
    public array $map = [
        'arrow' => '➡️',
        'bug' => '🐞',
        'done' => '✅',
        'download' => '⬇️',
        'downtrend' => '📉',
        'health' => '🧪',
        'major' => '🔴' ,
        'minor' => '🟡',
        'patch' => '🟣',
        'package' => '📦',
        'report' => '📊',
        'search' => '🔍',
        'star' => '⭐',
        'warning' => '⚠️',
        'waiting' => '⏳'
    ];

    public static function get($name): string
    {
        return (new self())->map[$name] ?? '';
    }
}