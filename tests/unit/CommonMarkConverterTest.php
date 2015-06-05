<?php

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\CommonMarkConverter;

class CommonMarkConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testCustomParser()
    {
        $environment = $this->getMock('League\CommonMark\Environment');
        $environment->expects($this->never())->method($this->equalTo('mergeConfig'));
        $environment->expects($this->any())->method('getBlockRendererForClass')->willReturn(new DocumentRenderer());

        $parser = $this->getMock('League\CommonMark\DocParser', array('parse', 'getEnvironment'), array($environment));
        $parser->expects($this->once())->method($this->equalTo('getEnvironment'))->willReturn($environment);
        $parser->expects($this->once())->method($this->equalTo('parse'))->willReturn(new Document());

        $converter = new CommonMarkConverter(array(), $parser);
        $converter->convertToHtml('');
    }

    public function testCustomRenderer()
    {
        $renderer = $this->getMock('League\CommonMark\HtmlRendererInterface');
        $renderer->expects($this->once())->method('renderBlock')->willReturn('');

        $converter = new CommonMarkConverter(array(), null, $renderer);
        $converter->convertToHtml('');
    }
}
