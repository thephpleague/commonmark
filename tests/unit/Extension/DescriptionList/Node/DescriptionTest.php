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

namespace League\CommonMark\Tests\Unit\Extension\DescriptionList\Node;

use League\CommonMark\Extension\DescriptionList\Node\Description;
use PHPUnit\Framework\TestCase;

final class DescriptionTest extends TestCase
{
    public function testTightnessConstructorGetterAndSetter(): void
    {
        $description = new Description();
        $this->assertFalse($description->isTight());

        $description->setTight(true);
        $this->assertTrue($description->isTight());

        $description->setTight(false);
        $this->assertFalse($description->isTight());

        $description = new Description(true);
        $this->assertTrue($description->isTight());
    }
}
