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

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\DocumentProcessorInterface;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\ConfigurationAwareInterface;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function testAddGetExtensions()
    {
        $environment = new Environment();
        $this->assertCount(0, $environment->getExtensions());

        $firstExtension = $this->createMock(ExtensionInterface::class);
        $firstExtension->expects($this->once())
            ->method('register')
            ->with($environment);

        $environment->addExtension($firstExtension);

        $extensions = $environment->getExtensions();
        $this->assertCount(1, $extensions);
        $this->assertEquals($firstExtension, $extensions[0]);

        $secondExtension = $this->createMock(ExtensionInterface::class);
        $secondExtension->expects($this->once())
            ->method('register')
            ->with($environment);
        $environment->addExtension($secondExtension);

        $extensions = $environment->getExtensions();

        $this->assertCount(2, $extensions);
        $this->assertEquals($firstExtension, $extensions[0]);
        $this->assertEquals($secondExtension, $extensions[1]);

        // Trigger initialization
        $environment->getBlockParsers();
        $environment->getInlineProcessors();
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

        $this->assertContains($renderer, $environment->getBlockRenderersForClass('MyClass'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddBlockRendererFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockRenderersForClass('MyClass');

        $renderer = $this->createMock('League\CommonMark\Block\Renderer\BlockRendererInterface');
        $environment->addBlockRenderer('MyClass', $renderer);
    }

    public function testInlineParserCanMatchRegexDelimiter()
    {
        $environment = new Environment();

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['/']));

        $environment->addInlineParser($parser);
        $environment->getInlineParsersForCharacter('/');

        $this->assertEquals(1, preg_match($environment->getInlineParserCharacterRegex(), 'foo/bar'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineParserFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineParsersForCharacter('');

        $parser = $this->createMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $environment->addInlineParser($parser);
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

        $this->assertContains($renderer, $environment->getInlineRenderersForClass('MyClass'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineRendererFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineRenderersForClass('MyClass');

        $renderer = $this->createMock('League\CommonMark\Inline\Renderer\InlineRendererInterface');
        $environment->addInlineRenderer('MyClass', $renderer);
    }

    public function testGetBlockRendererForNonExistantClass()
    {
        $environment = new Environment();

        $renderer = $environment->getBlockRenderersForClass('MyClass');

        $this->assertEmpty($renderer);
    }

    public function testGetInlineRendererForNonExistantClass()
    {
        $environment = new Environment();

        $renderer = $environment->getInlineRenderersForClass('MyClass');

        $this->assertEmpty($renderer);
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
        $environment->getInlineRenderersForClass('MyClass');

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

    public function testGetInlineParserCharacterRegexForEmptyEnvironment()
    {
        $environment = new Environment();

        // This triggers the initialization which builds the regex
        $environment->getInlineParsersForCharacter('');

        $regex = $environment->getInlineParserCharacterRegex();

        $test = '*This* should match **everything** including chars like `[`.';
        $matches = [];
        preg_match($regex, $test, $matches);
        $this->assertSame($test, $matches[0]);
    }

    public function testInjectablesGetInjected()
    {
        $environment = new Environment();

        $parser = $this->getMockBuilder([BlockParserInterface::class, EnvironmentAwareInterface::class, ConfigurationAwareInterface::class])->getMock();
        $parser->expects($this->once())->method('setEnvironment')->with($environment);
        $parser->expects($this->once())->method('setConfiguration');

        $environment->addBlockParser($parser);

        // Trigger initialization
        $environment->getBlockParsers();
    }

    public function testBlockParserPrioritization()
    {
        $environment = new Environment();

        $parser1 = $this->createMock(BlockParserInterface::class);
        $parser2 = $this->createMock(BlockParserInterface::class);
        $parser3 = $this->createMock(BlockParserInterface::class);

        $environment->addBlockParser($parser1);
        $environment->addBlockParser($parser2, 50);
        $environment->addBlockParser($parser3);

        $parsers = iterator_to_array($environment->getBlockParsers());

        $this->assertSame($parser2, $parsers[0]);
        $this->assertSame($parser1, $parsers[1]);
        $this->assertSame($parser3, $parsers[2]);
    }

    public function testInlineParserPrioritization()
    {
        $environment = new Environment();

        $parser1 = $this->createMock(InlineParserInterface::class);
        $parser1->method('getCharacters')->willReturn(['a']);
        $parser2 = $this->createMock(InlineParserInterface::class);
        $parser2->method('getCharacters')->willReturn(['a']);
        $parser3 = $this->createMock(InlineParserInterface::class);
        $parser3->method('getCharacters')->willReturn(['a']);

        $environment->addInlineParser($parser1);
        $environment->addInlineParser($parser2, 50);
        $environment->addInlineParser($parser3);

        $parsers = iterator_to_array($environment->getInlineParsersForCharacter('a'));

        $this->assertSame($parser2, $parsers[0]);
        $this->assertSame($parser1, $parsers[1]);
        $this->assertSame($parser3, $parsers[2]);
    }

    public function testInlineProcessorPrioritization()
    {
        $environment = new Environment();

        $processor1 = $this->createMock(InlineProcessorInterface::class);
        $processor2 = $this->createMock(InlineProcessorInterface::class);
        $processor3 = $this->createMock(InlineProcessorInterface::class);

        $environment->addInlineProcessor($processor1);
        $environment->addInlineProcessor($processor2, 50);
        $environment->addInlineProcessor($processor3);

        $parsers = iterator_to_array($environment->getInlineProcessors());

        $this->assertSame($processor2, $parsers[0]);
        $this->assertSame($processor1, $parsers[1]);
        $this->assertSame($processor3, $parsers[2]);
    }

    public function testDocumentProcessorPrioritization()
    {
        $environment = new Environment();

        $processor1 = $this->createMock(DocumentProcessorInterface::class);
        $processor2 = $this->createMock(DocumentProcessorInterface::class);
        $processor3 = $this->createMock(DocumentProcessorInterface::class);

        $environment->addDocumentProcessor($processor1);
        $environment->addDocumentProcessor($processor2, 50);
        $environment->addDocumentProcessor($processor3);

        $parsers = iterator_to_array($environment->getDocumentProcessors());

        $this->assertSame($processor2, $parsers[0]);
        $this->assertSame($processor1, $parsers[1]);
        $this->assertSame($processor3, $parsers[2]);
    }

    public function testBlockRendererPrioritization()
    {
        $environment = new Environment();

        $renderer1 = $this->createMock(BlockRendererInterface::class);
        $renderer2 = $this->createMock(BlockRendererInterface::class);
        $renderer3 = $this->createMock(BlockRendererInterface::class);

        $environment->addBlockRenderer('foo', $renderer1);
        $environment->addBlockRenderer('foo', $renderer2, 50);
        $environment->addBlockRenderer('foo', $renderer3);

        $parsers = iterator_to_array($environment->getBlockRenderersForClass('foo'));

        $this->assertSame($renderer2, $parsers[0]);
        $this->assertSame($renderer1, $parsers[1]);
        $this->assertSame($renderer3, $parsers[2]);
    }

    public function testInlineRendererPrioritization()
    {
        $environment = new Environment();

        $renderer1 = $this->createMock(InlineRendererInterface::class);
        $renderer2 = $this->createMock(InlineRendererInterface::class);
        $renderer3 = $this->createMock(InlineRendererInterface::class);

        $environment->addInlineRenderer('foo', $renderer1);
        $environment->addInlineRenderer('foo', $renderer2, 50);
        $environment->addInlineRenderer('foo', $renderer3);

        $parsers = iterator_to_array($environment->getInlineRenderersForClass('foo'));

        $this->assertSame($renderer2, $parsers[0]);
        $this->assertSame($renderer1, $parsers[1]);
        $this->assertSame($renderer3, $parsers[2]);
    }
}
