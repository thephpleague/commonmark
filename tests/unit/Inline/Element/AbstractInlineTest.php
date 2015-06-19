<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Inline\Element;

class AbstractInlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to League\CommonMark\Inline\Element\AbstractInline::setParent() must be an instance of League\CommonMark\Block\Element\AbstractBlock or League\CommonMark\Block\Element\AbstractInline, instance of stdClass given
     */
    public function testSetParent()
    {
        $inline = $this->getMockForAbstractClass('League\CommonMark\Inline\Element\AbstractInline');
        $inline->setParent(new \StdClass());
    }
}
