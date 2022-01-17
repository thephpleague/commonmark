<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Xml;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Xml\MarkdownToXmlConverter;
use PHPUnit\Framework\TestCase;

final class MarkdownToXmlConverterTest extends TestCase
{
    public function testConvert(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownToXmlConverter($environment);

        $actual = $converter->convert('# Hello World');

        $expected = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<document xmlns="http://commonmark.org/xml/1.0">
    <heading level="1">
        <text>Hello World</text>
    </heading>
</document>

XML;

        $this->assertInstanceOf(RenderedContentInterface::class, $actual);
        $this->assertSame($expected, $actual->getContent());
    }

    public function testInvokeReturnsSameOutputAsConvert(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownToXmlConverter($environment);

        $inputMarkdown = '# Hello World';

        $this->assertEquals($converter->convert($inputMarkdown), $converter($inputMarkdown));
    }
}
