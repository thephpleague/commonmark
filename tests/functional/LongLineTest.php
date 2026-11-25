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

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * When a regex hits a PCRE limit (JIT stack, recursion or backtracking), preg_* returns false, which
 * reads as "no match". These lines are long enough to hit those limits under the default settings,
 * with or without JIT, so they would silently render differently if a regex regressed.
 */
final class LongLineTest extends TestCase
{
    private const LENGTH = 100000;

    /**
     * @dataProvider provideTestCases
     *
     * @param array<string, mixed> $config
     */
    #[DataProvider('provideTestCases')]
    public function testLongLine(string $input, string $expectedOutput, array $config = []): void
    {
        $this->assertSame($expectedOutput, \trim((new CommonMarkConverter($config))->convert($input)->getContent()));
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2?: array<string, mixed>}>
     */
    public static function provideTestCases(): iterable
    {
        $a = \str_repeat('a', self::LENGTH);

        yield 'thematic break' => [\str_repeat('-', self::LENGTH), '<hr />'];
        yield 'thematic break with spaces' => [\str_repeat('- ', \intdiv(self::LENGTH, 2)), '<hr />'];
        yield 'thematic break with tabs' => [\str_repeat("*\t", \intdiv(self::LENGTH, 2)), '<hr />'];

        yield 'link destination in braces' => ['[a](<' . $a . '>)', '<p><a href="' . $a . '">a</a></p>'];
        yield 'link destination in braces followed by another' => [
            '[a](<' . $a . '>) [b](<ok>)',
            '<p><a href="' . $a . '">a</a> <a href="ok">b</a></p>',
        ];

        yield 'reference definition destination in braces' => ['[a]: <' . $a . ">\n\n[a]", '<p><a href="' . $a . '">a</a></p>'];
        yield 'reference definition title' => ['[a]: /u "' . $a . "\"\n\n[a]", '<p><a href="/u" title="' . $a . '">a</a></p>'];
        yield 'reference definition title over several lines' => [
            "[a]: /u \"x\n" . $a . "\n\"\n\n[a]",
            '<p><a href="/u" title="x' . "\n" . $a . "\n" . '">a</a></p>',
        ];

        $labels = 'b' . \str_repeat('.c', \intdiv(self::LENGTH, 2));

        yield 'email autolink with many labels' => [
            '<http://ok.example> <a@' . $labels . '>',
            '<p><a href="http://ok.example">http://ok.example</a> <a href="mailto:a@' . $labels . '">a@' . $labels . '</a></p>',
        ];

        $local = \str_repeat('a', 256000);

        yield 'email autolink followed by inline HTML' => [
            '<' . $local . '@b.c> <b>x</b>',
            '<p><a href="mailto:' . $local . '@b.c">' . $local . '@b.c</a> <b>x</b></p>',
        ];

        $tagName = \str_repeat('a', 300000);

        yield 'long tag name followed by inline HTML' => [
            'x <' . $tagName . '=> <b>z</b>',
            '<p>x &lt;' . $tagName . '=&gt; <b>z</b></p>',
        ];

        $info = \str_repeat('a', 1100000);

        yield 'fenced code with a long info string' => [
            '```' . $info . "\n<script>\n```",
            '<pre><code class="language-' . $info . '">&lt;script&gt;' . "\n" . '</code></pre>',
            ['html_input' => 'allow'],
        ];
    }
}
