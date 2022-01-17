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
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverter;
use Michelf\Markdown;
use Michelf\MarkdownExtra;

$config = [
    'exec'         => array_shift($argv),
    'md'           => sprintf('%s/sample.md', __DIR__),
    'iterations'   => 20,
    'parser'       => [],
    'csv'          => false,
    'flags'        => [
        'isolate'  => false,
        'memory'   => false,
    ],
];

$usage = function (array $config, string $format, ...$args) {
    $errmsg = vsprintf("Error: {$format}", $args);

    fwrite(
        STDERR,
        <<<EOD
{$errmsg}:
Usage: {$config['exec']} [options] [flags]
Options:
    --md         file          default: sample.md
    --iterations num           default: 20
    --parser     name          default: all
    --csv        fd|file       default: disabled
Flags:
    -isolate                   default: off
    -memory                    default: off, implies isolate

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
                    $usage($config, 'invalid option %s', $key);
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
                   $usage($config, 'invalid flag %s', $key);
               }

               $config['flags'][$key] = true;
        } break;

        default: $usage($config, $key);
    }
}

$config['iterations'] = max($config['iterations'], 20);

if ($config['md'] !== '-' && !file_exists($config['md'])) {
    $usage($config, 'cannot read input %s', $config['md']);
}

if ($config['flags']['memory']) {
    $config['flags']['isolate'] = true;
}

if ($config['flags']['isolate'] && !function_exists('proc_open')) {
    $usage($config, 'isolation requires proc_open');
}

if ($config['csv'] !== false) {
    $stream = ctype_digit($config['csv']) ?
        "php://fd/{$config['csv']}" :
        realpath($config['csv']);

    $fd = @fopen(
        $stream,
        $config['exec'] === 'exec' ?
            'w' : 'w+'
    );

    if (!is_resource($fd)) {
        $usage(
            $config,
            'cannot fopen(%s) for writing',
            $config['csv']
        );
    }

    define('CSVOUT', $fd);

    if ($config['exec'] !== 'exec') {
        fputcsv(
            CSVOUT,
            ['Name', 'CPU', 'MEM']
        );
        fflush(CSVOUT);
    }
}

if ($config['md'] === '-') {
    $config['md'] = stream_get_contents(STDIN);
} else {
    $config['md'] = file_get_contents($config['md']);
}

$parsers = [
    'CommonMark' => function ($markdown) {
        $parser = new CommonMarkConverter();
        $parser->convert($markdown);
    },
    'CommonMark GFM' => function ($markdown) {
        $parser = new GithubFlavoredMarkdownConverter();
        $parser->convert($markdown);
    },
    'CommonMark All Extensions' => function ($markdown) {
        $environment = new Environment([
            'default_attributes' => [
                Table::class => [
                    'class' => 'table',
                ],
            ],
            'external_link' => [
                'internal_hosts'     => 'www.example.com',
                'open_in_new_window' => true,
                'html_class'         => 'external-link',
                'nofollow'           => '',
                'noopener'           => 'external',
                'noreferrer'         => 'external',
            ],
            'mentions' => [
                'github_handle' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => 'https://github.com/%s',
                ],
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new DefaultAttributesExtension());
        $environment->addExtension(new DescriptionListExtension());
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $environment->addExtension(new ExternalLinkExtension());
        $environment->addExtension(new FootnoteExtension());
        $environment->addExtension(new FrontMatterExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new MentionExtension());
        $environment->addExtension(new SmartPunctExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TableOfContentsExtension());
        $environment->addExtension(new TaskListExtension());

        $parser = new MarkdownConverter($environment);
        $parser->convert($markdown);
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
            \CommonMark\Parse($markdown)
        );
    };
}

if (count($config['parser'])) {
    $parsers = array_filter($parsers, function ($parser) use ($config) {
        return array_search($parser, $config['parser']) > -1;
    }, ARRAY_FILTER_USE_KEY);
}

$exec = function (array $config, string $parser) use ($parsers): array {
    $parse = $parsers[$parser];

    $start = microtime(true);
    for ($i = 0; $i < $config['iterations']; $i++) {
        echo '.';
        $parse($config['md']);
    }
    $end = microtime(true);

    $cpu = ($end - $start) * 1000;
    $cpu /= $config['iterations'];
    $cpu = round($cpu, 2);

    if ($config['flags']['memory']) {
        $mem = memory_get_peak_usage();
        $mem /= 1024;
    } else {
        $mem = 0;
    }

    if ($config['csv']) {
        fputcsv(
            CSVOUT,
            [$parser, $cpu, $mem]
        );
        fflush(CSVOUT);
    }

    return [$cpu, $mem];
};

if ($config['exec'] === 'exec') {
    vfprintf(
        STDERR,
        '%.2f %d',
        $exec($config, array_shift($config['parser']))
    );
    exit(0);
}

$run = function (array $config, string $parser) use ($exec): array {
    if ($config['flags']['isolate']) {
        $bin = str_replace(' ', '\ ', realpath($config['exec']));
        $argv =
            '--exec exec ' .
            "--parser \"{$parser}\" " .
            '--md - ' .
            "--iterations {$config['iterations']}";

        if ($config['csv']) {
            $argv .= " --csv {$config['csv']}";
        }

        if ($config['flags']['memory']) {
            $argv .= ' -memory';
        }

        $proc = proc_open("{$bin} {$argv}", [
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

            if (proc_close($proc) !== 0) {
                fprintf(STDERR, 'failed to close process%s', PHP_EOL);
                exit(1);
            }
        } else {
            fprintf(STDERR, 'failed to open process%s', PHP_EOL);
            exit(1);
        }

        return explode(" ", $result);
    }

    return $exec($config, $parser);
};

$display = function (array $config, string $title, array $fmt, array $results, string $formatName, string $formatResult): void {
    $space = $config['iterations'] - 7;
    $position = 1;
    $top = 0;
    $diff = 0;

    vprintf($title, $fmt);

    asort($results);
    foreach ($results as $name => $result) {
        if (!$top) {
            $top = $result;
        } else {
            $diff = $top - $result;
        }

        printf(
            "\t%d) $formatName $formatResult % {$space}s%s",
            $position++,
            $name,
            $result,
            $diff ?
                sprintf($formatResult, $diff) :
                null,
            PHP_EOL
        );
    }
};

if (extension_loaded('xdebug')) {
    fwrite(STDERR, 'The xdebug extension is loaded, this can significantly skew benchmarks. Disable it for accurate results. For xdebug 3, prefix your command with "XDEBUG_MODE=off"' . PHP_EOL . PHP_EOL);
}

printf(
    'Running Benchmarks (%s), %d Implementations, %d Iterations:%s',
    $config['flags']['isolate'] ?
           'Isolated' : 'No Isolation',
    count($parsers),
    $config['iterations'],
    PHP_EOL
);

$cpu = [];
$mem = [];
foreach ($parsers as $name => $parser) {
    printf("\t%- 30s ", $name);

    [$cpu[$name], $mem[$name]] =
        $run($config, $name);

    echo PHP_EOL;
    usleep(300000);
}

$display(
    $config,
    '%sBenchmark Results, CPU:%s',
    [PHP_EOL, PHP_EOL],
    $cpu,
    '%- 26s',
    '% 6.2f ms'
);

if (!$config['flags']['memory']) {
    exit(0);
}

$display(
    $config,
    '%sBenchmark Results, Peak Memory:%s',
    [PHP_EOL, PHP_EOL],
    $mem,
    '%- 26s',
    '% 6d kB'
);
