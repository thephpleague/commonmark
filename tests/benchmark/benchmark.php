<?php

/*
 * This file is part of the commonmark-php package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use ColinODell\CommonMark\CommonMarkConverter;

$markdown = file_get_contents(__DIR__ . '/' . 'sample.md');

$parsers = array(
    'commonmark-php' => function ($markdown) {
        $parser = new CommonMarkConverter();
        $parser->convertToHtml($markdown);
    }
);

$iterations = 20;
$results = array();
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
    printf("%-18s | %4d ms\n", $name, $ms);
}

