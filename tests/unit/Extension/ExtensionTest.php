<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension;

use PHPUnit\Framework\TestCase;

class ExtensionTest extends TestCase
{
    public function testDefaultMethodBehavior()
    {
        $extension = $this->getMockForAbstractClass('League\CommonMark\Extension\Extension');
        $this->assertEquals([], $extension->getBlockParsers());
        $this->assertEquals([], $extension->getBlockRenderers());
        $this->assertEquals([], $extension->getInlineParsers());
        $this->assertEquals([], $extension->getInlineProcessors());
        $this->assertEquals([], $extension->getInlineRenderers());
        $this->assertEquals([], $extension->getDocumentProcessors());
    }
}
