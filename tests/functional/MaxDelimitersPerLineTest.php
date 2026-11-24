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

final class MaxDelimitersPerLineTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     */
    #[DataProvider('provideTestCases')]
    public function testIt(string $input, int $maxDelimsPerLine, string $expectedOutput): void
    {
        $converter = new CommonMarkConverter(['max_delimiters_per_line' => $maxDelimsPerLine]);

        $this->assertEquals($expectedOutput, \trim($converter->convert($input)->getContent()));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function provideTestCases(): iterable
    {
        // Each delimiter character counts against the limit, not each run
        yield ['*a* **b *c* b**', 6, '<p><em>a</em> **b <em>c</em> b**</p>'];
        yield ['*a* **b *c* b**', 8, '<p><em>a</em> <strong>b <em>c</em> b</strong></p>'];

        yield ['*a* **b *c **d** c* b**', 0, '<p>*a* **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 1, '<p>*a* **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 2, '<p><em>a</em> **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 3, '<p><em>a</em> **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 8, '<p><em>a</em> **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 9, '<p><em>a</em> **b *c <strong>d</strong> c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 10, '<p><em>a</em> **b <em>c <strong>d</strong> c</em> b**</p>'];
        yield ['*a* **b *c **d** c* b**', 11, '<p><em>a</em> **b <em>c <strong>d</strong> c</em> b**</p>'];
        yield ['*a* **b *c **d** c* b**', 12, '<p><em>a</em> <strong>b <em>c <strong>d</strong> c</em> b</strong></p>'];
        yield ['*a* **b *c **d** c* b**', 100, '<p><em>a</em> <strong>b <em>c <strong>d</strong> c</em> b</strong></p>'];

        // Long runs can't be used to produce deeply-nested emphasis beyond the limit
        $run = \str_repeat('_', 200);

        yield [$run . '.' . $run, 399, '<p>' . $run . '.' . $run . '</p>'];
        yield [$run . '.' . $run, 400, '<p>' . \str_repeat('<strong>', 100) . '.' . \str_repeat('</strong>', 100) . '</p>'];

        // Once a run exceeds the remaining limit, later (shorter) delimiters on the line are ignored too
        yield ['__________x__________ *a*', 12, '<p>__________x__________ *a*</p>'];

        // Link and image bracket openers are tracked separately and are not limited by this option
        yield ['[text](https://example.com)', 0, '<p><a href="https://example.com">text</a></p>'];
        yield ['![alt](https://example.com/x.png)', 0, '<p><img src="https://example.com/x.png" alt="alt" /></p>'];
    }
}
