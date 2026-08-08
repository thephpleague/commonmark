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
 * The spec allows at most 999 characters inside the square brackets of a link label.
 * That limit applies when a reference definition is stored and also when one is looked up,
 * so both sides must agree on where the boundary sits and on how characters are counted.
 */
final class LinkLabelLengthTest extends TestCase
{
    private const LIMIT = 999;

    /**
     * @dataProvider provideTestCases
     */
    #[DataProvider('provideTestCases')]
    public function testLabelLengthBoundary(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, \trim((new CommonMarkConverter())->convert($input)->getContent()));
    }

    /**
     * @return iterable<array<string>>
     */
    public static function provideTestCases(): iterable
    {
        foreach (['ascii' => 'a', 'multibyte' => 'é'] as $type => $char) {
            $atLimit = \str_repeat($char, self::LIMIT);
            $tooLong = \str_repeat($char, self::LIMIT + 1);

            // Shortcut references
            yield 'shortcut, ' . $type . ', at the limit' => [
                '[' . $atLimit . ']: /url' . "\n\n" . '[' . $atLimit . ']',
                '<p><a href="/url">' . $atLimit . '</a></p>',
            ];

            yield 'shortcut, ' . $type . ', over the limit' => [
                '[' . $tooLong . ']: /url' . "\n\n" . '[' . $tooLong . ']',
                '<p>[' . $tooLong . ']: /url</p>' . "\n" . '<p>[' . $tooLong . ']</p>',
            ];

            // Collapsed references
            yield 'collapsed, ' . $type . ', at the limit' => [
                '[' . $atLimit . ']: /url' . "\n\n" . '[' . $atLimit . '][]',
                '<p><a href="/url">' . $atLimit . '</a></p>',
            ];

            yield 'collapsed, ' . $type . ', over the limit' => [
                '[' . $tooLong . ']: /url' . "\n\n" . '[' . $tooLong . '][]',
                '<p>[' . $tooLong . ']: /url</p>' . "\n" . '<p>[' . $tooLong . '][]</p>',
            ];
        }

        // Full references (the second label is matched by a non-unicode regex, so only
        // the ASCII form reaches the limit as a character count)
        $atLimit = \str_repeat('a', self::LIMIT);
        $tooLong = \str_repeat('a', self::LIMIT + 1);

        yield 'full, ascii, at the limit' => [
            '[' . $atLimit . ']: /url' . "\n\n" . '[text][' . $atLimit . ']',
            '<p><a href="/url">text</a></p>',
        ];

        yield 'full, ascii, over the limit' => [
            '[' . $tooLong . ']: /url' . "\n\n" . '[text][' . $tooLong . ']',
            '<p>[' . $tooLong . ']: /url</p>' . "\n" . '<p>[text][' . $tooLong . ']</p>',
        ];
    }

    /**
     * The limit counts the raw characters between the brackets, not the normalized ones, so an
     * over-long run of whitespace must not resolve even though it collapses down to a label that
     * does exist. (This mirrors what the bracketed `[text][label]` form has always done.)
     */
    public function testWhitespaceIsCountedBeforeItIsCollapsed(): void
    {
        $converter = new CommonMarkConverter();

        $atLimit = '[a b]: /url' . "\n\n" . '[a' . \str_repeat(' ', self::LIMIT - 2) . 'b]';
        $this->assertStringContainsString('<a href="/url">', $converter->convert($atLimit)->getContent());

        $tooLong = '[a b]: /url' . "\n\n" . '[a' . \str_repeat(' ', self::LIMIT - 1) . 'b]';
        $this->assertStringNotContainsString('<a href="/url">', $converter->convert($tooLong)->getContent());
    }
}
