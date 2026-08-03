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

    public function testDeeplyNestedDocumentDoesNotExplodeOutputSize(): void
    {
        // Set an explicit limit so this test keeps its teeth if the default ever drops below the depth used here
        $environment = new Environment(['max_nesting_level' => 100_000]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownToXmlConverter($environment);

        $depth = 500;
        $xml   = $converter->convert(\str_repeat('> ', $depth) . "x\n")->getContent();

        // Without the indentation cap this document renders as ~1,000 KiB of mostly-whitespace XML
        $this->assertLessThan(128 * 1024, \strlen($xml));

        // Capping the indentation must not drop or unbalance any tags
        $this->assertSame($depth, \substr_count($xml, '<block_quote>'));
        $this->assertSame($depth, \substr_count($xml, '</block_quote>'));

        // LIBXML_PARSEHUGE is needed because libxml refuses to parse beyond 256 levels of nesting
        $this->assertTrue((new \DOMDocument())->loadXML($xml, \LIBXML_PARSEHUGE));
    }

    public function testMaxIndentationLevelIsConfigurable(): void
    {
        $markdown = "> > > > x\n";

        $this->assertStringContainsString("\n<text>x</text>", $this->convertWithMaxIndentationLevel($markdown, 0));
        $this->assertStringContainsString("\n    <text>x</text>", $this->convertWithMaxIndentationLevel($markdown, 1));
        $this->assertStringContainsString("\n                        <text>x</text>", $this->convertWithMaxIndentationLevel($markdown, 100));
    }

    private function convertWithMaxIndentationLevel(string $markdown, int $maxIndentationLevel): string
    {
        $environment = new Environment(['xml' => ['max_indentation_level' => $maxIndentationLevel]]);
        $environment->addExtension(new CommonMarkCoreExtension());

        return (new MarkdownToXmlConverter($environment))->convert($markdown)->getContent();
    }
}
