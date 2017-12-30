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

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Environment;
use League\CommonMark\InlineParserEngine;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function testAddGetExtensions()
    {
        $firstExtension = $this->getMockForAbstractClass('League\CommonMark\Extension\ExtensionInterface');

        $environment = new Environment();
        $this->assertCount(0, $environment->getExtensions());

        $environment->addExtension($firstExtension);

        $extensions = $environment->getExtensions();
        $this->assertCount(1, $extensions);
        $this->assertEquals($firstExtension, $extensions[0]);

        $secondExtension = $this->getMockForAbstractClass('League\CommonMark\Extension\ExtensionInterface');
        $environment->addExtension($secondExtension);

        $extensions = $environment->getExtensions();
        $this->assertCount(2, $extensions);
        $this->assertEquals($firstExtension, $extensions[0]);
        $this->assertEquals($secondExtension, $extensions[1]);
    }

    public function testConstructor()
    {
        $config = ['foo' => 'bar'];
        $environment = new Environment($config);
        $this->assertEquals('bar', $environment->getConfig('foo'));
    }

    public function testGetConfig()
    {
        $config = [
            'foo' => 'bar',
            'a'   => [
                'b' => 'c',
            ],
        ];
        $environment = new Environment($config);

        // No arguments should return the whole thing
        $this->assertEquals($config, $environment->getConfig());

        // Test getting a single scalar element
        $this->assertEquals('bar', $environment->getConfig('foo'));

        // Test getting a single array element
        $this->assertEquals($config['a'], $environment->getConfig('a'));

        // Test getting an element by path
        $this->assertEquals('c', $environment->getConfig('a/b'));

        // Test getting a path that's one level too deep
        $this->assertNull($environment->getConfig('a/b/c'));

        // Test getting a non-existent element
        $this->assertNull($environment->getConfig('test'));

        // Test getting a non-existent element with a default value
        $this->assertEquals(42, $environment->getConfig('answer', 42));
    }

    public function testSetConfig()
    {
        $environment = new Environment(['foo' => 'bar']);
        $environment->setConfig(['test' => '123']);
        $this->assertNull($environment->getConfig('foo'));
        $this->assertEquals('123', $environment->getConfig('test'));
    }

    public function testSetConfigAfterInit()
    {
        $this->expectException('RuntimeException');

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockParsers();
        $environment->setConfig(['foo' => 'bar']);
    }

    public function testMergeConfig()
    {
        $environment = new Environment(['foo' => 'bar', 'test' => '123']);
        $environment->mergeConfig(['test' => '456']);
        $this->assertEquals('bar', $environment->getConfig('foo'));
        $this->assertEquals('456', $environment->getConfig('test'));
    }

    public function testMergeConfigAfterInit()
    {
        $this->expectException('RuntimeException');

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockParsers();
        $environment->mergeConfig(['foo' => 'bar']);
    }

    public function testAddBlockParserAndGetter()
    {
        $environment = new Environment();

        $parser = $this->createMock('League\CommonMark\Block\Parser\BlockParserInterface');
        $environment->addBlockParser($parser);

        $this->assertContains($parser, $environment->getBlockParsers());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddBlockParserFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockParsers();

        $parser = $this->createMock('League\CommonMark\Block\Parser\BlockParserInterface');
        $environment->addBlockParser($parser);
    }

    public function testAddBlockRenderer()
    {
        $environment = new Environment();

        $renderer = $this->createMock('League\CommonMark\Block\Renderer\BlockRendererInterface');
        $environment->addBlockRenderer('MyClass', $renderer);

        $this->assertEquals($renderer, $environment->getBlockRendererForClass('MyClass'));
    }

    public function testSwapBuiltInBlockRenderer()
    {
        $environment = new Environment();

        $mockRenderer = $this->createMock('League\CommonMark\Block\Renderer\BlockRendererInterface');

        $builtInClasses = [
            'BlockQuote'    => 'League\CommonMark\Block\Element\BlockQuote',
            'Document'      => 'League\CommonMark\Block\Element\Document',
            'FencedCode'    => 'League\CommonMark\Block\Element\FencedCode',
            'Heading'       => 'League\CommonMark\Block\Element\Heading',
            'HtmlBlock'     => 'League\CommonMark\Block\Element\HtmlBlock',
            'IndentedCode'  => 'League\CommonMark\Block\Element\IndentedCode',
            'ListBlock'     => 'League\CommonMark\Block\Element\ListBlock',
            'ListItem'      => 'League\CommonMark\Block\Element\ListItem',
            'Paragraph'     => 'League\CommonMark\Block\Element\Paragraph',
            'ThematicBreak' => 'League\CommonMark\Block\Element\ThematicBreak',
        ];

        foreach ($builtInClasses as $name => $fullyQualifiedName) {
            $environment->addBlockRenderer($name, $mockRenderer);
        }

        foreach ($builtInClasses as $name => $fullyQualifiedName) {
            $this->assertEquals(
                $mockRenderer,
                $environment->getBlockRendererForClass($fullyQualifiedName)
            );
        }
    }

    public function testSwapBuiltInInlineRenderer()
    {
        $environment = new Environment();

        $mockRenderer = $this->createMock('League\CommonMark\Inline\Renderer\InlineRendererInterface');

        $builtInClasses = [
            'Code'       => 'League\CommonMark\Inline\Element\Code',
            'Emphasis'   => 'League\CommonMark\Inline\Element\Emphasis',
            'HtmlInline' => 'League\CommonMark\Inline\Element\HtmlInline',
            'Image'      => 'League\CommonMark\Inline\Element\Image',
            'Link'       => 'League\CommonMark\Inline\Element\Link',
            'Newline'    => 'League\CommonMark\Inline\Element\Newline',
            'Strong'     => 'League\CommonMark\Inline\Element\Strong',
            'Text'       => 'League\CommonMark\Inline\Element\Text',
        ];

        foreach ($builtInClasses as $name => $fullyQualifiedName) {
            $environment->addInlineRenderer($name, $mockRenderer);
        }

        foreach ($builtInClasses as $name => $fullyQualifiedName) {
            $this->assertEquals(
                $mockRenderer,
                $environment->getInlineRendererForClass($fullyQualifiedName)
            );
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddBlockRendererFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockRendererForClass('MyClass');

        $renderer = $this->createMock('League\CommonMark\Block\Renderer\BlockRendererInterface');
        $environment->addBlockRenderer('MyClass', $renderer);
    }

    public function testAddInlineParserAndGetter()
    {
        $environment = new Environment();

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['a']));

        $environment->addInlineParser($parser);

        $this->assertContains($parser, $environment->getInlineParsers());
    }

    public function testInlineParserCanMatchRegexDelimiter()
    {
        $environment = new Environment();

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['/']));

        $environment->addInlineParser($parser);
        $environment->getInlineParsers();

        $this->assertEquals(1, preg_match($environment->getInlineParserCharacterRegex(), 'foo/bar'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineParserFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineParsers();

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $environment->addInlineParser($parser);
    }

    public function testGetInlineParserByName()
    {
        $environment = new Environment();

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['a']));

        $environment->addInlineParser($parser);

        $this->assertEquals($parser, $environment->getInlineParser('test'));
    }

    public function testGetInlineParsersForCharacter()
    {
        $environment = new Environment();

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['a']));

        $environment->addInlineParser($parser);

        $this->assertContains($parser, $environment->getInlineParsersForCharacter('a'));
    }

    public function testGetInlineParsersForNonExistantCharacter()
    {
        $environment = new Environment();

        $this->assertEmpty($environment->getInlineParsersForCharacter('a'));
    }

    public function testAddInlineProcessor()
    {
        $environment = new Environment();

        $processor = $this->createMock('League\CommonMark\Inline\Processor\InlineProcessorInterface');
        $environment->addInlineProcessor($processor);

        $this->assertContains($processor, $environment->getInlineProcessors());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineProcessorFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineProcessors();

        $processor = $this->createMock('League\CommonMark\Inline\Processor\InlineProcessorInterface');
        $environment->addInlineProcessor($processor);
    }

    public function testAddInlineRenderer()
    {
        $environment = new Environment();

        $renderer = $this->createMock('League\CommonMark\Inline\Renderer\InlineRendererInterface');
        $environment->addInlineRenderer('MyClass', $renderer);

        $this->assertEquals($renderer, $environment->getInlineRendererForClass('MyClass'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineRendererFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineRendererForClass('MyClass');

        $renderer = $this->createMock('League\CommonMark\Inline\Renderer\InlineRendererInterface');
        $environment->addInlineRenderer('MyClass', $renderer);
    }

    public function testGetBlockRendererForNonExistantClass()
    {
        $environment = new Environment();

        $renderer = $environment->getBlockRendererForClass('MyClass');

        $this->assertNull($renderer);
    }

    public function testGetInlineRendererForNonExistantClass()
    {
        $environment = new Environment();

        $renderer = $environment->getInlineRendererForClass('MyClass');

        $this->assertNull($renderer);
    }

    public function testCreateInlineParserEngine()
    {
        $environment = new Environment();

        $engine = $environment->createInlineParserEngine();

        $this->assertTrue($engine instanceof InlineParserEngine);
    }

    public function testAddExtensionAndGetter()
    {
        $environment = new Environment();

        $extension = $this->createMock('League\CommonMark\Extension\ExtensionInterface');
        $environment->addExtension($extension);

        $this->assertContains($extension, $environment->getExtensions());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddExtensionFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineRendererForClass('MyClass');

        $extension = $this->createMock('League\CommonMark\Extension\ExtensionInterface');
        $environment->addExtension($extension);
    }

    public function testAddDocumentProcessor()
    {
        $environment = new Environment();

        $processor = $this->createMock('League\CommonMark\DocumentProcessorInterface');
        $environment->addDocumentProcessor($processor);

        $this->assertContains($processor, $environment->getDocumentProcessors());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddDocumentProcessorFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getDocumentProcessors();

        $processor = $this->createMock('League\CommonMark\DocumentProcessorInterface');
        $environment->addDocumentProcessor($processor);
    }

    public function testExtensionBetweenNonExtensions()
    {
        $environment = new Environment();

        $processor = $this->createMock('League\CommonMark\DocumentProcessorInterface');
        $environment->addDocumentProcessor($processor);
        $this->assertCount(1, $environment->getExtensions());

        $extension = $this->createMock('League\CommonMark\Extension\ExtensionInterface');
        $environment->addExtension($extension);
        $this->assertCount(2, $environment->getExtensions());

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $environment->addInlineParser($parser);
        $this->assertCount(3, $environment->getExtensions());

        $this->assertInstanceOf('League\CommonMark\Extension\MiscExtension', $environment->getExtensions()[0]);
        $this->assertInstanceOf('League\CommonMark\Extension\ExtensionInterface', $environment->getExtensions()[1]);
        $this->assertInstanceOf('League\CommonMark\Extension\MiscExtension', $environment->getExtensions()[2]);
    }

    public function testGetInlineParserCharacterRegexForEmptyEnvironment()
    {
        $environment = new Environment();

        // This triggers the initialization which builds the regex
        $environment->createInlineParserEngine();

        $regex = $environment->getInlineParserCharacterRegex();

        $test = '*This* should match **everything** including chars like `[`.';
        $matches = [];
        preg_match($regex, $test, $matches);
        $this->assertSame($test, $matches[0]);
    }
}
