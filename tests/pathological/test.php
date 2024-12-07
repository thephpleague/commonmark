#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/../../vendor/autoload.php';

$cases = [
    'U+0000 in input' => [
        'sizes' => [1],
        'input' => static fn($n) => "abc\u{0000}def\u{0000}\n",
        'expected' => static fn($n) => "<p>abc\u{FFFD}def\u{FFFD}</p>",
    ],
    'Alternate line endings' => [
        'sizes' => [1],
        'input' => static fn($n) => "- a\n- b\r- c\r\n- d",
        'expected' => static fn($n) => "<ul>\n<li>a</li>\n<li>b</li>\n<li>c</li>\n<li>d</li>\n</ul>\n",
    ],
    'Nested strong emphasis' => [
        'sizes' => [50, 500],
        'input' => static fn($n) => \str_repeat('*a **a ', $n) . 'b' . \str_repeat(' a** a*', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('<em>a <strong>a ', $n) . 'b' . \str_repeat(' a</strong> a</em>', $n) . '</p>',
    ],
    'Emphasis closers with no openers' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('a_ ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('a_ ', $n - 1) . 'a_</p>',
    ],
    'Emphasis openers with no closers' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('_a ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('_a ', $n - 1) . '_a</p>',
    ],
    'Openers and closers multiple of 3' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => 'a**b' . \str_repeat('c* ', $n),
        'expected' => static fn($n) => '<p>a**b' . \str_repeat('c* ', $n - 1) . 'c*</p>',
    ],
    'Delimiters that cannot open or close' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/172',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('*_* _ ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('<em>_</em> _ ', $n - 1) . '<em>_</em> _</p>',
    ],
    'Link closers with no openers' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('a] ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('a] ', $n - 1) . 'a]</p>',
    ],
    'Link openers with no closers' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('[a ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('[a ', $n - 1) . '[a</p>',
    ],
    'Link openers and emphasis closers' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('[ a_ ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('[ a_ ', $n - 1) . '[ a_</p>',
    ],
    'Mismatched openers and closers' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('*a_ ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('*a_ ', $n - 1) . '*a_</p>',
    ],
    'Pattern [ (](' => [
        'sizes' => [500, 5_000, 50_000],
        'input' => static fn($n) => \str_repeat('[ (](', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('[ (](', $n) . '</p>',
    ],
    'Nested brackets' => [
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('[', $n) . 'a' . \str_repeat(']', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('[', $n) . 'a' . \str_repeat(']', $n) . '</p>',
    ],
    'Backslash in link' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/157',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => '[' . \str_repeat('\\', $n) . "\n",
        'expected' => static fn($n) => '<p>[' . \str_repeat('\\', $n / 2) . '</p>',
    ],
    'Backslash in unclosed link title' => [
        'sizes' => [10, 100, 1_000],
        'input' => static fn($n) => '[test](\\url "' . \str_repeat('\\', $n) . "\n",
        'expected' => static fn($n) => '<p>[test](\\url &quot;' . \str_repeat('\\', $n / 2) . '</p>',
    ],
    'Unclosed inline links (1)' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/129',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('[](', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('[](', $n) . '</p>',
    ],
    'Unclosed inline links (2)' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/129',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('[a](b', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('[a](b', $n) . '</p>',
    ],
    'Unclosed inline links (3)' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/129',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('[a](<b', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat('[a](&lt;b', $n) . '</p>',
    ],
    'Nested blockquotes' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/129',
        'sizes' => [100, 1_000],
        'input' => static fn($n) => \str_repeat('> ', $n) . "a\n",
        'expected' => static fn($n) => \str_repeat("<blockquote>\n", $n) . "<p>a</p>\n" . \str_repeat("</blockquote>\n", $n),
    ],
    'Backticks' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/129',
        'sizes' => [500, 1_000, 2_000, 4_000],
        'input' => static fn($n) => \implode('', \array_map(static fn($i) => 'e' . \str_repeat('`', $i), \range(1, $n))),
    ],
    'Many ref. definitions' => [
        'ref' => 'https://github.com/commonmark/commonmark.js/issues/129',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat("[a]: u\n", $n),
    ],
    'Huge horizontal rule' => [
        'sizes' => [500, 5_000],
        'input' => static fn($n) => \str_repeat('*', $n) . "\n",
        'expected' => static fn($n) => '<hr />',
    ],
    'CVE-2022-39209' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-cgh3-p57x-9q7q',
        'extension' => 'autolink',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('![l', $n) . "\n",
        'expected' => static fn($n) => '<p>' . \str_repeat('![l', $n) . '</p>',
    ],
    'CVE-2023-22486' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-r572-jvj2-3m8p',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('![[]()', $n) . "\n",
        'expected' => static fn($n) => '<p>' . \str_repeat('![<a href=""></a>', $n) . '</p>',
    ],
    'CVE-2023-22484' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-24f7-9frr-5h2r',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => '</' . \str_repeat('<!--', $n) . "\n",
        'expected' => static fn($n) => '<p>&lt;/' . \str_repeat('&lt;!--', $n) . '</p>',
    ],
    'CVE-2023-22483 test 1' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => "-1" . \str_repeat("<?x", $n) . "y\n",
        'expected' => static fn($n) => '<p>-1' . \str_repeat('&lt;?x', $n) . 'y</p>',
    ],
    'CVE-2023-22483 test 2 (tables)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'extension' => 'table',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat("|-\nt>|-\n", $n) . "y\n",
        'expected' => static fn($n) => '<p>' . \str_repeat("|-\nt&gt;|-\n", $n) . 'y</p>',
    ],
    'CVE-2023-22483 test 3' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [1_000, 10_000, 20_000, 40_000],
        'input' => static fn($n) => \str_repeat('*t ', $n) . \str_repeat('_t*_ ', $n) . "\n",
        'expected' => static fn($n) => '<p>' . \str_repeat('<em>t ', $n) . \str_repeat('_t</em>_ ', $n - 1) . '_t</em>_</p>',
    ],
    'CVE-2023-22483 test 4 (tables)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'extension' => 'table',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => "x\n| - |\n" . \str_repeat("|", $n) . "y\n",
        'expected' => static fn($n) => "<p>x\n| - |\n" . \str_repeat('|', $n) . 'y</p>',
    ],
    'CVE-2023-22483 test 5 (autolink)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('z_www.', $n) . "\n",
    ],
    'CVE-2023-22483 test 6' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [100, 1_000, 10_000, 100_000],
        'input' => static fn($n) => "[f]:u\n\"\n" . \str_repeat("[f]\n", $n) . "[f]:u \"t\n",
    ],
    'CVE-2023-22483 test 7 (autolink)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => "www." . \str_repeat(")", $n) . "\n",
        'expected' => static fn($n) => '<p>www.' . \str_repeat(')', $n) . '</p>',
    ],
    'CVE-2023-22483 test 8 (tables)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'extension' => 'table',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => ":-\n" . \str_repeat(":\n", $n) . ":-\n",
        'expected' => static fn($n) => "<p>:-\n" . \str_repeat(":\n", $n) . ':-</p>',
    ],
    'CVE-2023-22483 test 9 (autolink)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat("<http://s", $n) . "\n",
        'expected' => static fn($n) => '<p>' . \str_repeat('&lt;http://s', $n) . '</p>',
    ],
    'CVE-2023-22483 test 10 (strikethrough)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => '~e' . \str_repeat(',z~~', $n) . "\n",
        'expected' => static fn($n) => '<p>~e' . \str_repeat(',z~~', $n) . '</p>',
    ],
    'CVE-2023-22483 test 11 (deeply-nested blockquotes)' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-29g3-96g3-jg6c',
        'sizes' => [100, 1_000, 2_000, 3_000],
        'input' => static fn($n) => \str_repeat(">", $n) . \str_repeat(".", $n) . "\n",
        'expected' => static fn($n) => \str_repeat("<blockquote>\n", $n) . '<p>' . \str_repeat('.', $n) . "</p>\n" . \str_repeat("</blockquote>\n", $n),
    ],
    'CVE-2023-24824 test 1' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-66g8-4hjf-77xh',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat(">", $n) . \str_repeat("a*", $n) . "\n",
        'configuration' => [
            'max_nesting_level' => 1_000,
        ],
    ],
    'CVE-2023-24824 test 2' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-66g8-4hjf-77xh',
        'sizes' => [500, 5_000, 50_000],
        'input' => static fn($n) => \str_repeat(" -", $n) . 'x' . \str_repeat("\n", $n),
        'configuration' => [
            'max_nesting_level' => 500,
        ],
    ],
    'CVE-2023-26485 test 1' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-r8vr-c48j-fcc5',
        'sizes' => [50, 500, 5_000], // ideally should be 1000, 10_000, 100_000 but recursive rendering makes large sizes fail
        'input' => static fn($n) => \str_repeat('_', $n) . '.' . \str_repeat('_', $n) . "\n",
        'expected' => static fn($n) => '<p>' . \str_repeat('<strong>', $n/2) . '.' . \str_repeat('</strong>', $n/2) . '</p>',
    ],
    'CVE-2023-26485 test 2' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-r8vr-c48j-fcc5',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => "1.\n" . \str_repeat(" 2.\n", $n),
        'expected' => static fn($n) => "<ol>\n<li></li>\n" . \str_repeat("<li></li>\n", $n) . "</ol>\n",
    ],
    'CVE-2023-26485 test 3' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-r8vr-c48j-fcc5',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat(" -\n", $n) . "x\n",
        'expected' => static fn($n) => "<ul>\n" . \str_repeat("<li></li>\n", $n) . "</ul>\n<p>x</p>",
    ],
    'CVE-2023-37463 test 1' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-w4qg-3vf7-m9x5',
        'extension' => 'table',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => '|' . \str_repeat('x|', $n) . "\n|" . \str_repeat('-|', $n) . "\n",
        'expected' => static fn($n) => "<table>\n<thead>\n<tr>\n" . \str_repeat("<th>x</th>\n", $n) . "</tr>\n</thead>\n</table>\n",
    ],
    'CVE-2023-37463 test 2' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-w4qg-3vf7-m9x5',
        'extension' => 'table',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => '|' . \str_repeat('x|', $n) . "\n|" . \str_repeat('-|', $n) . "\n" . \str_repeat("a\n", $n),
    ],
    'CVE-2023-37463 test 3' => [
        'ref' => 'https://github.com/github/cmark-gfm/security/advisories/GHSA-w4qg-3vf7-m9x5',
        'extension' => 'footnotes',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat("[^1]:\n", $n) . \str_repeat("\n", $n),
        'expected' => static fn($n) => '',
    ],
];

print("Running " . \count($cases) . " pathological test cases\n\n");

$failures = [];
foreach ($cases as $name => $case) {
    print("\033[1m$name\033[0m");
    if (isset($case['ref'])) {
        print(" (\033[4;34m{$case['ref']}\033[0m)");
    }
    print("\n");

    $lastRunTime = null;
    $lastInputSize = null;
    $lastOutputSize = null;
    foreach ($case['sizes'] as $size) {
        printf('  - %s: ', number_format($size));

        $input     = $case['input']($size);
        $inputSize = \strlen($input);

        if ($lastInputSize === null) {
            $timeout = 5; // 5 seconds
        } else {
            // Ideally, these cases should run in linear time or better,
            // but we'll allow a 50% margin of error locally (or 2x in CI)
            if (isset($_ENV['CI']) || isset($_SERVER['CI'])) {
                $timeout = \ceil($lastRunTime * $inputSize / $lastInputSize * 2);
            } else {
                $timeout = \ceil($lastRunTime * $inputSize / $lastInputSize * 1.5);
            }
            // But regardless of this, we always want to wait at least 5 seconds,
            // and at most 60 seconds.
            $timeout = \max(5, \min(60, $timeout));
        }

        if (isset($_ENV['CI']) || isset($_SERVER['CI'])) {
            $command = ['php', 'convert.php', \json_encode($case['configuration'] ?? [])];
        } else {
            $command = ['php', '-n', 'convert.php', \json_encode($case['configuration'] ?? [])];
        }

        if (isset($case['extension'])) {
            $command[] = $case['extension'];
        }

        $process = new Process($command, __DIR__, null, $input, $timeout);

        $startTime = \microtime(true);
        try {
            $result = $process->run();
            $executionTime = \round(\microtime(true) - $startTime, 3);

            if ($lastRunTime !== null) {
                $relativeDifference = \round($executionTime / $lastRunTime, 1);
                printf("%.3f seconds (%sx slower); ", $executionTime, $relativeDifference);
            } else {
                printf("%.3f seconds; ", $executionTime);
            }

            $lastRunTime = $executionTime;

            $actual = $process->getOutput();
            $actualOutputSize = strlen($actual);

            if ($lastOutputSize !== null && $lastOutputSize !== 0 && $lastInputSize !== null) {
                $relativeDifference = \round($actualOutputSize / $lastOutputSize, 1);
                printf("%.1fkb output (%sx larger)", $actualOutputSize / 1024, $relativeDifference);
                if ($actualOutputSize > ($lastOutputSize * $inputSize / $lastInputSize * 1.5)) {

                    printf(" \033[31;31m%s\033[0m", 'Max allowed size exceeded');
                    $failures[$name] = 'Max allowed size exceeded';
                }
            } else {
                printf("%.1fkb output", $actualOutputSize / 1024);
            }
            echo "\n";

            $lastOutputSize = $actualOutputSize;

            if (isset($case['expected'])) {
                $expected = $case['expected']($size);
                if (\trim($expected) !== \trim($actual)) {
                    printf("    \033[31;31m%s\033[0m\n", 'Unexpected output');
                    $failures[$name] = 'Unexpected output';
                }
            }

            if ($result !== 0) {
                printf("    \033[31;31m%s\033[0m\n", 'Process failed: ' . $process->getErrorOutput());
                $failures[$name] = 'Process failed';
            }
        } catch (ProcessTimedOutException $e) {
            printf("\033[31;31m%s\033[0m\n", 'Max execution time ('.$timeout.'s) exceeded');
            $failures[$name] = 'Max execution time exceeded';
        } catch (ProcessSignaledException $e) {
            printf("\033[31;31m%s\033[0m\n", 'Process signaled: ' . $e->getSignal());
            $failures[$name] = 'Process signaled';
        } finally {
            $lastInputSize = \strlen($input);
        }
    }
    print("\n");
}

print("\n");
print("--------------------\n");
print("\n");
print(\count($failures) . " failure(s) out of " . \count($cases) . " test(s)\n");

if (\count($failures) > 0) {
    exit(1);
}
