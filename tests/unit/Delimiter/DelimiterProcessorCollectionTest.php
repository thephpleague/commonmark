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

namespace League\CommonMark\Tests\Unit\Delimiter;

use League\CommonMark\Delimiter\Processor\DelimiterProcessorCollection;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DelimiterProcessorCollectionTest extends TestCase
{
    public function testAddNewProcessor(): void
    {
        $collection = new DelimiterProcessorCollection();

        $processor1 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor1->method('getOpeningCharacter')->willReturn('*');
        $processor1->method('getClosingCharacter')->willReturn('*');
        $collection->add($processor1);

        $processor2 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor2->method('getOpeningCharacter')->willReturn('_');
        $processor2->method('getClosingCharacter')->willReturn('_');
        $collection->add($processor2);

        $this->assertSame($processor1, $collection->getDelimiterProcessor('*'));
        $this->assertSame($processor2, $collection->getDelimiterProcessor('_'));
    }

    public function testAddProcessorForCharacterAlreadyRegistered(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Delim processor for character "*" already exists');

        $collection = new DelimiterProcessorCollection();

        $processor1 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor1->method('getOpeningCharacter')->willReturn('*');
        $collection->add($processor1);

        $processor2 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor2->method('getOpeningCharacter')->willReturn('*');
        $collection->add($processor2);
    }
}
