<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\TableOfContents;

use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents as NewTableOfContents;
use League\CommonMark\Extension\TableOfContents\TableOfContents as DeprecatedTableOfContents;
use PHPUnit\Framework\TestCase;

/**
 * @group legacy
 */
final class TableOfContentsDeprecationTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testBothClassesAreInFactTheSameType()
    {
        self::assertInstanceOf(DeprecatedTableOfContents::class, new NewTableOfContents(new ListData()));
        self::assertInstanceOf(NewTableOfContents::class, new DeprecatedTableOfContents(new ListData()));
    }

    /**
     * @group legacy
     */
    public function testAClassConsumingTheNewTypeCanReceiveTheOldType()
    {
        new class(new DeprecatedTableOfContents(new ListData())) {
            public function __construct(NewTableOfContents $foo)
            {
            }
        };
        self::assertTrue(true); // Look, Ma! No crash!
    }

    /**
     * @group legacy
     */
    public function testAClassConsumingTheOldTypeCanReceiveTheNewType()
    {
        new class(new NewTableOfContents(new ListData())) {
            public function __construct(DeprecatedTableOfContents $foo)
            {
            }
        };
        self::assertTrue(true); // Look, Ma! No crash!
    }

    /**
     * @group legacy
     */
    public function testDeprecatedClassWorksLikeUsual()
    {
        $this->assertInstanceOf(DeprecatedTableOfContents::class, new DeprecatedTableOfContents(new ListData()));
    }

    /**
     * @group legacy
     */
    public function testNewClassWorksAsExpected()
    {
        $this->assertInstanceOf(NewTableOfContents::class, new NewTableOfContents(new ListData()));
    }
}
