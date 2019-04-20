<?php

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
use PHPUnit\Framework\TestCase;

class DelimiterProcessorCollectionTest extends TestCase
{
    public function testAddNewProcessor()
    {
        $collection = new DelimiterProcessorCollection();

        $processor1 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor1->method('getCharacter')->willReturn('*');
        $collection->add($processor1);

        $processor2 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor2->method('getCharacter')->willReturn('_');
        $collection->add($processor2);

        $this->assertSame($processor1, $collection->getDelimiterProcessor('*'));
        $this->assertSame($processor2, $collection->getDelimiterProcessor('_'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Delim processor for character "*" already exists
     */
    public function testAddProcessorForCharacterAlreadyRegistered()
    {
        $collection = new DelimiterProcessorCollection();

        $processor1 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor1->method('getCharacter')->willReturn('*');
        $collection->add($processor1);

        $processor2 = $this->getMockForAbstractClass(DelimiterProcessorInterface::class);
        $processor2->method('getCharacter')->willReturn('*');
        $collection->add($processor2);
    }
}
