#!/usr/bin/env php
<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Warm benchmark: measures only parsing, not converter initialization.
 *
 * Usage:
 *   php tests/benchmark/benchmark_parse.php [--iterations N] [--warmup N] [--md file]
 *
 * Defaults: 100 iterations, 10 warmup rounds, all built-in corpus files.
 */
require_once __DIR__ . '/../../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;

if (extension_loaded('xdebug') && getenv('XDEBUG_MODE') !== 'off') {
    fwrite(STDERR, 'Xdebug is active — results will be skewed. Use XDEBUG_MODE=off.' . PHP_EOL . PHP_EOL);
}

// --- Argument parsing ---

$iterations = 100;
$warmup     = 10;
$files      = [];

for ($i = 1; $i < $argc; $i++) {
    switch ($argv[$i]) {
        case '--iterations':
            $iterations = (int) ($argv[++$i] ?? $iterations);
            break;
        case '--warmup':
            $warmup = (int) ($argv[++$i] ?? $warmup);
            break;
        case '--md':
            $files[] = $argv[++$i] ?? '';
            break;
        default:
            fwrite(STDERR, "Unknown option: {$argv[$i]}" . PHP_EOL);
            fwrite(STDERR, 'Usage: benchmark_parse.php [--iterations N] [--warmup N] [--md file]' . PHP_EOL);
            exit(1);
    }
}

if ($files === []) {
    $files = [
        __DIR__ . '/sample.md',
    ];
}

// --- Benchmark runner ---

/**
 * @return array{min: float, median: float, p95: float, mean: float}
 */
function bench(callable $fn, int $warmup, int $iterations): array
{
    for ($i = 0; $i < $warmup; $i++) {
        $fn();
    }

    $times = [];
    for ($i = 0; $i < $iterations; $i++) {
        $t0      = \hrtime(true);
        $fn();
        $times[] = (\hrtime(true) - $t0) / 1e6; // ms
    }

    \sort($times);

    $count  = \count($times);
    $mean   = \array_sum($times) / $count;
    $median = $count % 2 === 0
        ? ($times[$count / 2 - 1] + $times[$count / 2]) / 2
        : $times[(int) ($count / 2)];
    $p95    = $times[(int) \ceil($count * 0.95) - 1];

    return ['min' => $times[0], 'median' => $median, 'p95' => $p95, 'mean' => $mean];
}

// --- Run ---

printf(
    'Warm benchmark — %d iterations (+%d warmup)%s%s',
    $iterations,
    $warmup,
    PHP_EOL,
    PHP_EOL
);
printf("%-52s %8s %8s %8s %8s%s", 'File', 'min', 'median', 'p95', 'mean', PHP_EOL);
printf("%s%s", \str_repeat('-', 88), PHP_EOL);

$converter = new CommonMarkConverter();

foreach ($files as $file) {
    if (! \is_file($file)) {
        fwrite(STDERR, "File not found: {$file}" . PHP_EOL);
        continue;
    }

    $markdown = \file_get_contents($file);
    $size     = \round(\strlen($markdown) / 1024, 1);
    $label    = \basename($file) . " ({$size} KB)";

    $stats = bench(
        static fn () => $converter->convert($markdown),
        $warmup,
        $iterations
    );

    printf(
        "%-52s %7.2f ms %7.2f ms %7.2f ms %7.2f ms%s",
        $label,
        $stats['min'],
        $stats['median'],
        $stats['p95'],
        $stats['mean'],
        PHP_EOL
    );
}