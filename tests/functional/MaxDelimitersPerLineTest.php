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
use PHPUnit\Framework\TestCase;

final class MaxDelimitersPerLineTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     */
    public function testIt(string $input, int $maxDelimsPerLine, string $expectedOutput): void
    {
        $converter = new CommonMarkConverter(['max_delimiters_per_line' => $maxDelimsPerLine]);

        $this->assertEquals($expectedOutput, \trim($converter->convert($input)->getContent()));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function provideTestCases(): iterable
    {
        yield ['*a* **b *c* b**', 6, '<p><em>a</em> <strong>b <em>c</em> b</strong></p>'];

        yield ['*a* **b *c **d** c* b**', 0, '<p>*a* **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 1, '<p>*a* **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 2, '<p><em>a</em> **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 3, '<p><em>a</em> **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 4, '<p><em>a</em> **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 5, '<p><em>a</em> **b *c **d** c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 6, '<p><em>a</em> **b *c <strong>d</strong> c* b**</p>'];
        yield ['*a* **b *c **d** c* b**', 7, '<p><em>a</em> **b <em>c <strong>d</strong> c</em> b**</p>'];
        yield ['*a* **b *c **d** c* b**', 8, '<p><em>a</em> <strong>b <em>c <strong>d</strong> c</em> b</strong></p>'];
        yield ['*a* **b *c **d** c* b**', 9, '<p><em>a</em> <strong>b <em>c <strong>d</strong> c</em> b</strong></p>'];
        yield ['*a* **b *c **d** c* b**', 100, '<p><em>a</em> <strong>b <em>c <strong>d</strong> c</em> b</strong></p>'];
    }
}
