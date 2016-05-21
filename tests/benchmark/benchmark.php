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

$iterations = 20;
$results = [];
foreach ($parsers as $name => $parser) {
    $start = microtime(true);
    for ($i = 0; $i <= $iterations; $i++) {
        echo '.';
        $parser($markdown);
    }

    $results[$name] = (microtime(true) - $start) * 1000 / $iterations;
}

asort($results);

printf("\n\n");
printf("===================================\n");
printf("Here are the average parsing times:\n", $iterations);
printf("===================================\n");
foreach ($results as $name => $ms) {
    printf("%-19s | %4d ms\n", $name, $ms);
}
