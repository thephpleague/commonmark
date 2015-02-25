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

namespace League\CommonMark\Tests\Block\Renderer;

use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Renderer\ListBlockRenderer;

class ListBlockRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ListBlockRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new ListBlockRenderer();
    }

    /**
     * @param int|null $listStart
     * @param mixed    $expectedAttributeValue
     *
     * @dataProvider dataForTestOrderedListStartingNumber
     */
    public function testOrderedListStartingNumber($listStart = null, $expectedAttributeValue = null)
    {
        $list = $this->createOrderedListBlock($listStart);
        $rendererStub = $this->getMockBuilder('League\CommonMark\HtmlRenderer')
            ->disableOriginalConstructor()
            ->getMock();

        $htmlElement = $this->renderer->render($list, $rendererStub);

        $this->assertSame($expectedAttributeValue, $htmlElement->getAttribute('start'));
    }

    public function dataForTestOrderedListStartingNumber()
    {
        return array(
            array(null, null),
            array(0, '0'),
            array(1, null),
            array(2, '2'),
            array(42, '42'),
        );
    }

    /**
     * @param int $start
     *
     * @return ListBlock
     */
    private function createOrderedListBlock($start)
    {
        $data = new ListData();
        $data->type = ListBlock::TYPE_ORDERED;
        $data->start = $start;

        return new ListBlock($data);
    }
} 