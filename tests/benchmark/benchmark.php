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

require_once __DIR__ . '/../../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use Michelf\Markdown;
use Michelf\MarkdownExtra;

$markdown = file_get_contents(__DIR__ . '/' . 'sample.md');

if (extension_loaded('xdebug')) {
    fwrite(STDERR, 'The xdebug extension is loaded, this can significantly skew benchmarks. Disable it for accurate results.' . PHP_EOL . PHP_EOL);
}

$parsers = [
    'CommonMark' => function ($markdown) {
        $parser = new CommonMarkConverter();
        $parser->convertToHtml($markdown);
    },
    'PHP Markdown' => function ($markdown) {
        Markdown::defaultTransform($markdown);
    },
    'PHP Markdown Extra' => function ($markdown) {
        MarkdownExtra::defaultTransform($markdown);
    },
    'Parsedown' => function ($markdown) {
        $parser = new Parsedown();
        $parser->text($markdown);
    },
    'cebe/markdown' => function ($markdown) {
        $parser = new \cebe\markdown\Markdown();
        $parser->parse($markdown);
    },
    'cebe/markdown gfm' => function ($markdown) {
        $parser = new \cebe\markdown\GithubMarkdown();
        $parser->parse($markdown);
    },
    'cebe/markdown extra' => function ($markdown) {
        $parser = new \cebe\markdown\MarkdownExtra();
        $parser->parse($markdown);
    },
];

if (extension_loaded('cmark')) {
    $parsers['krakjoe/cmark'] = function ($markdown) {
        \CommonMark\Render\HTML(
            \CommonMark\Parse($markdown));
    };
}

$iterations = isset($argv[1]) ? max($argv[1], 20) : 20;
$results = [];

printf('Running Benchmarks, %d Implementations, %d Iterations:%s',
       count($parsers), $iterations, PHP_EOL);

foreach ($parsers as $name => $parser) {
    printf("\t%- 30s ", $name);
    $start = microtime(true);
    for ($i = 0; $i <= $iterations; $i++) {
        echo '.';
        $parser($markdown);
    }
    $results[$name] = (microtime(true) - $start) * 1000 / $iterations;
    echo PHP_EOL;
    usleep(300000);
}
echo PHP_EOL;

asort($results);

$position = 1;
$top = 0;
$diffSpace = $iterations - 7;

printf('Benchmark Results:%s', PHP_EOL);
$diff = 0;
foreach ($results as $name => $ms) {
    if (!$top) {
        $top = $ms;
    } else {
        $diff = $top - $ms;
    }

    printf("\t%d) %- 26s % 6.2f ms% {$diffSpace}s%s",
        $position++,
        $name,
        $ms,
        $diff ?
            sprintf(' % 6.2f ms', $diff) :
            null,
        PHP_EOL);
}
