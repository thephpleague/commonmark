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
    'Multibyte leading whitespace' => [
        'extension' => 'commonmark',
        'sizes' => [300_000],
        'input' => static fn($n) => \str_repeat(' ', $n) . 'é',
        'expected' => static fn($n) => '<pre><code>' . \str_repeat(' ', $n - 4) . "é\n</code></pre>\n",
    ],
    'Multibyte inline delimiters' => [
        'extension' => 'commonmark',
        'sizes' => [100_000],
        'input' => static fn($n) => 'é' . \str_repeat('a_', $n),
        'expected' => static fn($n) => '<p>é' . \str_repeat('a_', $n) . "</p>\n",
    ],
    'Repeated invalid autolink prefixes' => [
        'extension' => 'autolink',
        'sizes' => [160_000],
        'input' => static fn($n) => 'é ' . \str_repeat('www. ', $n),
        'expected' => static fn($n) => '<p>é ' . \str_repeat('www. ', $n - 1) . "www.</p>\n",
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
    'Emphasis closers with varying run lengths' => [
        // A pile of openers that can never close, followed by closer runs of strictly
        // increasing length. Each distinct length must not mint a new openersBottom cache
        // key, otherwise every closer re-scans the entire pile.
        'extension' => 'commonmark',
        'sizes' => [25_000, 800_000],
        'input' => static function ($n) {
            $closers = '';
            for ($i = 1, $d = (int) \sqrt($n / 4.5); $i <= $d; $i++) {
                // Length 3i-1 keeps every closer at 2 (mod 3) so the "multiple of 3" rule
                // rejects every opener in the pile.
                $closers .= 'x' . \str_repeat('*', 3 * $i - 1) . 'y';
            }

            return \str_repeat('*a ', \intdiv($n - \strlen($closers), 3)) . $closers;
        },
    ],
    'Strikethrough closers with varying run lengths' => [
        // As above, but the pile is made of '*' openers which no '~' closer can ever match.
        'sizes' => [25_000, 800_000],
        'input' => static function ($n) {
            $closers = '';
            for ($l = 3, $k = (int) \sqrt(2 * $n / 3); $l < 3 + $k; $l++) {
                $closers .= 'a' . \str_repeat('~', $l) . ' ';
            }

            return \str_repeat(' *a', \intdiv($n - \strlen($closers), 3)) . $closers;
        },
    ],
    'Highlight closers with varying run lengths' => [
        // As above, but the pile is made of '*' openers which no '=' closer can ever match.
        // getDelimiterUse() returns 0 for every opener once the closer exceeds 2 characters,
        // so the cache key must not distinguish those longer closers from each other.
        'extension' => 'highlight',
        'sizes' => [25_000, 800_000],
        'input' => static function ($n) {
            $closers = '';
            for ($l = 3, $k = (int) \sqrt(2 * $n / 3); $l < 3 + $k; $l++) {
                $closers .= 'a' . \str_repeat('=', $l) . ' ';
            }

            return \str_repeat(' *a', \intdiv($n - \strlen($closers), 3)) . $closers;
        },
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
    'Nested brackets with a reference definition' => [
        // A single definition disables the empty-reference-map shortcut in ReferenceMap::get(),
        // so every closing bracket would otherwise normalize the whole span it encloses.
        'extension' => 'commonmark',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => "[x]: y\n\n" . \str_repeat('[', $n) . 'a' . \str_repeat(']', $n),
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
    'Backtick fence with a backtick in the info string' => [
        'extension' => 'commonmark',
        'sizes' => [10_000, 40_000, 160_000],
        // The trailing backtick is essential: it makes the "no backtick in the info string"
        // check fail, which is what forces the fence-length matching to be retried.
        'input' => static fn($n) => \str_repeat('`', $n / 2) . \str_repeat('a', $n / 2) . "`\n",
        'expected' => static fn($n) => '<p>' . \str_repeat('`', $n / 2) . \str_repeat('a', $n / 2) . '`</p>',
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
    'Nested list markers with trailing blanks (limited nesting)' => [
        'ref' => 'https://github.com/thephpleague/commonmark/issues/243',
        'sizes' => [1_000, 10_000, 100_000],
        'input' => static fn($n) => \str_repeat('- ', $n) . "x\n" . \str_repeat("\n", $n),
        'configuration' => [
            'max_nesting_level' => 100,
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
    'Duplicate footnote definitions' => [
        'extension' => 'footnotes',
        'sizes' => [200, 2_000, 20_000],
        'input' => static fn($n) => \str_repeat('[^a] ', $n) . "\n\n" . \str_repeat("[^a]: x\n\n", $n),
    ],
    'Footnote labels containing key path delimiters' => [
        'extension' => 'footnotes',
        'sizes' => [64, 256, 1_024],
        // Every label below is a distinct spelling of the same "." / "/" separated path, so any
        // implementation storing backrefs under a delimited key path collapses them onto a single
        // shared entry and generates a quadratic number of backrefs.
        'input' => static function ($n) {
            $spell = static function (int $bits) {
                $label = 'a';
                for ($i = 0; $i < 10; $i++) {
                    $label .= ($bits >> $i) & 1 ? '.' : '/';
                    $label .= 'a';
                }

                return $label;
            };

            $refs = $definitions = '';
            for ($i = 0; $i < $n; $i++) {
                $refs        .= '[^' . $spell($i) . '] ';
                $definitions .= '[^' . $spell($i) . "]: x\n\n";
            }

            return $refs . "\n\n" . $definitions;
        },
    ],
    'Duplicate heading permalink slugs' => [
        'extension' => 'heading-permalink',
        'sizes' => [1_000, 4_000, 16_000],
        'input' => static fn($n) => \str_repeat("# a\n\n", $n),
    ],
    'Duplicate inline footnote labels' => [
        'extension' => 'footnotes',
        'sizes' => [1_000, 4_000, 16_000],
        'input' => static fn($n) => \str_repeat("^[a]\n\n", $n),
    ],
    'Unpaired smart quotes' => [
        // Every quote is preceded by a letter and followed by a space, making it can-close-only,
        // so none of them ever pair up and all of them survive to ReplaceUnpairedQuotesListener.
        // The long runs of text between them matter: the listener merges each replacement into
        // its left neighbour, so the cost must depend on the bytes added, not on the bytes already
        // accumulated in that neighbour.
        'extension' => 'smartpunct',
        'sizes' => [4_000, 16_000, 64_000],
        'input' => static fn($n) => \str_repeat(\str_repeat('a', 63) . '" ', $n),
        'expected' => static fn($n) => '<p>' . \str_repeat(\str_repeat('a', 63) . "\u{201C} ", $n - 1) . \str_repeat('a', 63) . "\u{201C}</p>",
    ],
    'Adjacent inline attributes at start of block' => [
        'extension' => 'attributes',
        'sizes' => [1_000, 4_000, 16_000],
        'input' => static fn($n) => \str_repeat('{#a}', $n),
        'expected' => static fn($n) => '<p id="a"></p>',
    ],
    'Adjacent inline attributes separated by text' => [
        'extension' => 'attributes',
        'sizes' => [1_000, 4_000, 16_000],
        'input' => static fn($n) => \str_repeat('x {#a}', $n),
        'expected' => static fn($n) => '<p id="a">' . \str_repeat('x', $n) . '</p>',
    ],
    'Adjacent attribute blocks' => [
        // The link reference definitions are load-bearing: they keep each attributes block at
        // its default TARGET_NEXT while erasing themselves from the AST, welding the blocks
        // into one contiguous sibling chain with nothing in it to stop a forward walk.
        'extension' => 'attributes',
        'sizes' => [1_000, 4_000, 16_000],
        'input' => static fn($n) => \str_repeat("{#a}\n[a]: u\n", $n),
        'expected' => static fn($n) => '',
    ],
    'Adjacent inline attribute classes at start of block' => [
        // Unlike '#id', which overwrites a scalar, '.class' appends to a list which is then
        // written to the target and read back by the next node.
        'extension' => 'attributes',
        'sizes' => [2_000, 8_000, 32_000],
        'input' => static fn($n) => \str_repeat('{.c}', $n),
        'expected' => static fn($n) => '<p class="' . \rtrim(\str_repeat('c ', $n)) . '"></p>',
    ],
    'Adjacent attribute blocks with classes' => [
        'extension' => 'attributes',
        'sizes' => [2_000, 8_000, 32_000],
        'input' => static fn($n) => \str_repeat("{.c}\n\n", $n),
        'expected' => static fn($n) => '',
    ],
    'Attribute block classes on consecutive lines' => [
        // Every line is merged into the same block by AttributesBlockContinueParser.
        'extension' => 'attributes',
        'sizes' => [2_000, 8_000, 32_000],
        'input' => static fn($n) => \str_repeat("{.c}\n", $n),
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
