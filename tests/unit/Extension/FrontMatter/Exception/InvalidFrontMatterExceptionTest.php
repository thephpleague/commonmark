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

namespace League\CommonMark\Tests\Unit\Extension\FrontMatter\Exception;

use League\CommonMark\Extension\FrontMatter\Exception\InvalidFrontMatterException;
use PHPUnit\Framework\TestCase;

final class InvalidFrontMatterExceptionTest extends TestCase
{
    public function testWrap(): void
    {
        $previous = new \RuntimeException('Something bad happened');

        $ex = InvalidFrontMatterException::wrap($previous);

        $this->assertSame('Failed to parse front matter: Something bad happened', $ex->getMessage());
        $this->assertSame($previous, $ex->getPrevious());
    }
}
