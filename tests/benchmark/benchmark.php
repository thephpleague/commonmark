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

$config = [
    'exec'         => array_shift($argv),
    'md'           => sprintf('%s/sample.md', __DIR__),
    'iterations'   => 20,
    'parser'       => [],
    'flags'        => [
        'isolate'  => false,
    ],
];

$usage = function (array $config, string $invalid) {
    fwrite(STDERR, <<<EOD
Error: invalid command line at {$invalid}:
Usage: {$config['exec']} [options] [flags]
Options:
    --md         file          default: sample.md
    --iterations num           default: 20
    --parser     name          default: all
Flags:
    -isolate                   default: off

EOD
);
    exit(1);
};

while ($key = array_shift($argv)) {
    switch ($key[0]) {
        case '-': switch ($key[1]) {
            case '-':
                $key = substr($key, 2);

                if (!isset($config[$key])) {
                    $usage($config, $key);
                }

                if (is_array($config[$key])) {
                    $config[$key][] = array_shift($argv);
                } else {
                    $config[$key] = array_shift($argv);
                }
            break;

            default:
               $key = substr($key, 1);

               if (!isset($config['flags'][$key])) {
                   $usage($config, $key);
               }

               $config['flags'][$key] = true;
        } break;

        default: $usage($config, $key);
    }
}

$config['iterations'] = max($config['iterations'], 20);

if ($config['md'] !== '-' && !file_exists($config['md'])) {
    $usage($config, 'md');
}

if ($config['flags']['isolate'] && !function_exists('proc_open')) {
    $usage($config, 'isolate');
}

if ($config['md'] === '-') {
    $config['md'] = stream_get_contents(STDIN);
} else {
    $config['md'] = file_get_contents($config['md']);
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

$parsers = array_filter($parsers, function ($parser) use ($config) {
    return !count($config['parser']) || array_search($parser, $config['parser']) > -1;
}, ARRAY_FILTER_USE_KEY);

$exec = function (array $config, string $parser) use ($parsers) : int {
    $parser = $parsers[$parser];

    $start = microtime(true);
    for ($i = 0; $i < $config['iterations']; $i++) {
        echo '.';
        $parser($config['md']);
    }
    $end = microtime(true);

    return ($end - $start) * 1000;
};

if ($config['exec'] === 'exec') {
    fwrite(STDERR,
        $exec($config, array_shift($config['parser'])));
    exit(0);
}

$run = function (array $config, string $parser) use ($exec) : int {
    if ($config['flags']['isolate']) {
        $proc = proc_open("{$config['exec']} --exec exec --parser \"{$parser}\" --md - --iterations {$config['iterations']}", [
            0 => ['pipe', 'r'],
            1 => STDOUT,
            2 => ['pipe', 'w'],
        ], $pipes);

        if (is_resource($proc)) {
            fwrite($pipes[0], $config['md']);
            fclose($pipes[0]);

            $result =
                stream_get_contents($pipes[2]);
            fclose($pipes[2]);
        }

        return !proc_close($proc) ? $result : -1;
    }

    return $exec($config, $parser);
};

if (extension_loaded('xdebug')) {
    fwrite(STDERR, 'The xdebug extension is loaded, this can significantly skew benchmarks. Disable it for accurate results.' . PHP_EOL . PHP_EOL);
}

printf('Running Benchmarks (%s), %d Implementations, %d Iterations:%s',
       $config['flags']['isolate'] ?
           'Isolated' : 'No Isolation',
       count($parsers), $config['iterations'], PHP_EOL);

$results = [];

foreach ($parsers as $name => $parser) {
    printf("\t%- 30s ", $name);
    $results[$name] =
        $run($config, $name) / $config['iterations'];
    echo PHP_EOL;
    usleep(300000);
}
echo PHP_EOL;

asort($results);

$position = 1;
$top = 0;
$diffSpace = $config['iterations'] - 7;

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
