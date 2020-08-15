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

use League\CommonMark\Block\Parser as BlockParser;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Environment;
use League\CommonMark\Event\AbstractEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Tests\Unit\Environment\FakeBlock1;
use League\CommonMark\Tests\Unit\Environment\FakeBlock3;
use League\CommonMark\Tests\Unit\Environment\FakeBlockParser;
use League\CommonMark\Tests\Unit\Environment\FakeBlockRenderer;
use League\CommonMark\Tests\Unit\Environment\FakeDelimiterProcessor;
use League\CommonMark\Tests\Unit\Environment\FakeInline1;
use League\CommonMark\Tests\Unit\Environment\FakeInline3;
use League\CommonMark\Tests\Unit\Environment\FakeInlineParser;
use League\CommonMark\Tests\Unit\Environment\FakeInlineRenderer;
use League\CommonMark\Tests\Unit\Event\FakeEvent;
use League\CommonMark\Tests\Unit\Event\FakeEventListener;
use League\CommonMark\Tests\Unit\Event\FakeEventListenerInvokable;
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
        $this->expectException(\RuntimeException::class);

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
        $this->expectException(\RuntimeException::class);

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockParsers();
        $environment->mergeConfig(['foo' => 'bar']);
    }

    public function testAddBlockParserAndGetter()
    {
        $environment = new Environment();

        $parser = $this->createMock(BlockParser\BlockParserInterface::class);
        $environment->addBlockParser($parser);

        $this->assertContains($parser, $environment->getBlockParsers());
    }

    public function testAddBlockParserFailsAfterInitialization()
    {
        $this->expectException(\RuntimeException::class);
        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockParsers();

        $parser = $this->createMock(BlockParser\BlockParserInterface::class);
        $environment->addBlockParser($parser);
    }

    public function testAddBlockRenderer()
    {
        $environment = new Environment();

        $renderer = $this->createMock(BlockRendererInterface::class);
        $environment->addBlockRenderer('MyClass', $renderer);

        $this->assertContains($renderer, $environment->getBlockRenderersForClass('MyClass'));
    }

    public function testAddBlockRendererFailsAfterInitialization()
    {
        $this->expectException(\RuntimeException::class);
        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockRenderersForClass('MyClass');

        $renderer = $this->createMock(BlockRendererInterface::class);
        $environment->addBlockRenderer('MyClass', $renderer);
    }

    public function testInlineParserCanMatchRegexDelimiter()
    {
        $environment = new Environment();

        $parser = $this->createMock(InlineParserInterface::class);
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['/']));

        $environment->addInlineParser($parser);
        $environment->getInlineParsersForCharacter('/');

        $this->assertEquals(1, preg_match($environment->getInlineParserCharacterRegex(), 'foo/bar'));
    }

    public function testAddInlineParserFailsAfterInitialization()
    {
        $this->expectException(\RuntimeException::class);
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineParsersForCharacter('');

        $parser = $this->createMock(InlineParserInterface::class);
        $environment->addInlineParser($parser);
    }

    public function testGetInlineParsersForCharacter()
    {
        $environment = new Environment();

        $parser = $this->createMock(InlineParserInterface::class);
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

    public function testAddDelimiterProcessor()
    {
        $environment = new Environment();

        $processor = $this->createMock(DelimiterProcessorInterface::class);
        $processor->method('getOpeningCharacter')->willReturn('*');
        $environment->addDelimiterProcessor($processor);

        $this->assertSame($processor, $environment->getDelimiterProcessors()->getDelimiterProcessor('*'));
    }

    public function testAddDelimiterProcessorFailsAfterInitialization()
    {
        $this->expectException(\RuntimeException::class);
        $environment = new Environment();

        // This triggers the initialization
        $environment->getDelimiterProcessors();

        $processor = $this->createMock(DelimiterProcessorInterface::class);
        $environment->addDelimiterProcessor($processor);
    }

    public function testAddInlineRenderer()
    {
        $environment = new Environment();

        $renderer = $this->createMock(InlineRendererInterface::class);
        $environment->addInlineRenderer('MyClass', $renderer);

        $this->assertContains($renderer, $environment->getInlineRenderersForClass('MyClass'));
    }

    public function testAddInlineRendererFailsAfterInitialization()
    {
        $this->expectException(\RuntimeException::class);
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineRenderersForClass('MyClass');

        $renderer = $this->createMock(InlineRendererInterface::class);
        $environment->addInlineRenderer('MyClass', $renderer);
    }

    public function testGetBlockRendererForUnknownClass()
    {
        $environment = new Environment();
        $mockRenderer = $this->createMock(BlockRendererInterface::class);
        $environment->addBlockRenderer(FakeBlock3::class, $mockRenderer);

        $this->assertEmpty($environment->getBlockRenderersForClass(FakeBlock1::class));
    }

    public function testGetBlockRendererForSubClass()
    {
        $environment = new Environment();
        $mockRenderer = $this->createMock(BlockRendererInterface::class);
        $environment->addBlockRenderer(FakeBlock1::class, $mockRenderer);

        // Ensure the parent renderer is returned
        $this->assertFirstResult($mockRenderer, $environment->getBlockRenderersForClass(FakeBlock3::class));
        // Check again to ensure any cached result is also the same
        $this->assertFirstResult($mockRenderer, $environment->getBlockRenderersForClass(FakeBlock3::class));
    }

    public function testGetInlineRendererForNonUnknownClass()
    {
        $environment = new Environment();
        $mockRenderer = $this->createMock(InlineRendererInterface::class);
        $environment->addInlineRenderer(FakeInline3::class, $mockRenderer);

        $this->assertEmpty($environment->getInlineRenderersForClass(FakeInline1::class));
    }

    public function testGetInlineRendererForSubClass()
    {
        $environment = new Environment();
        $mockRenderer = $this->createMock(InlineRendererInterface::class);
        $environment->addInlineRenderer(FakeInline1::class, $mockRenderer);

        // Ensure the parent renderer is returned
        $this->assertFirstResult($mockRenderer, $environment->getInlineRenderersForClass(FakeInline3::class));
        // Check again to ensure any cached result is also the same
        $this->assertFirstResult($mockRenderer, $environment->getInlineRenderersForClass(FakeInline3::class));
    }

    public function testAddExtensionAndGetter()
    {
        $environment = new Environment();

        $extension = $this->createMock(ExtensionInterface::class);
        $environment->addExtension($extension);

        $this->assertContains($extension, $environment->getExtensions());
    }

    public function testAddExtensionFailsAfterInitialization()
    {
        $this->expectException(\RuntimeException::class);
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineRenderersForClass('MyClass');

        $extension = $this->createMock(ExtensionInterface::class);
        $environment->addExtension($extension);
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

    public function testGetInlineParserCharacterRegexForAsciiCharacters(): void
    {
        $environment = new Environment();

        $parser1 = $this->createMock(InlineParserInterface::class);
        $parser1->method('getCharacters')->willReturn(['*']);
        $environment->addInlineParser($parser1);

        $parser2 = $this->createMock(InlineParserInterface::class);
        $parser2->method('getCharacters')->willReturn(['[']);
        $environment->addInlineParser($parser2);

        // This triggers the initialization which builds the regex
        $environment->getInlineParsersForCharacter('');

        $regex = $environment->getInlineParserCharacterRegex();

        $this->assertSame('/^[^\*\[]+/', $regex);
    }

    public function testGetInlineParserCharacterRegexForMultibyteCharacters(): void
    {
        $environment = new Environment();

        $parser1 = $this->createMock(InlineParserInterface::class);
        $parser1->method('getCharacters')->willReturn(['*']);
        $environment->addInlineParser($parser1);

        $parser2 = $this->createMock(InlineParserInterface::class);
        $parser2->method('getCharacters')->willReturn(['★']);
        $environment->addInlineParser($parser2);

        // This triggers the initialization which builds the regex
        $environment->getInlineParsersForCharacter('');

        $regex = $environment->getInlineParserCharacterRegex();

        $this->assertSame('/^[^\*★]+/u', $regex);
    }

    public function testInjectableBlockParsersGetInjected()
    {
        $environment = new Environment();

        $parser = new FakeBlockParser();
        $environment->addBlockParser($parser);

        // Trigger initialization
        $environment->getBlockParsers();

        $this->assertSame($environment, $parser->getEnvironment());
        $this->assertNotNull($parser->getConfig());
    }

    public function testInjectableBlockRenderersGetInjected()
    {
        $environment = new Environment();

        $renderer = new FakeBlockRenderer();
        $environment->addBlockRenderer('', $renderer);

        // Trigger initialization
        $environment->getBlockParsers();

        $this->assertSame($environment, $renderer->getEnvironment());
        $this->assertNotNull($renderer->getConfig());
    }

    public function testInjectableInlineParsersGetInjected()
    {
        $environment = new Environment();

        $parser = new FakeInlineParser();
        $environment->addInlineParser($parser);

        // Trigger initialization
        $environment->getBlockParsers();

        $this->assertSame($environment, $parser->getEnvironment());
        $this->assertNotNull($parser->getConfig());
    }

    public function testInjectableInlineRenderersGetInjected()
    {
        $environment = new Environment();

        $renderer = new FakeInlineRenderer();
        $environment->addInlineRenderer('', $renderer);

        // Trigger initialization
        $environment->getBlockParsers();

        $this->assertSame($environment, $renderer->getEnvironment());
        $this->assertNotNull($renderer->getConfig());
    }

    public function testInjectableDelimiterProcessorsGetInjected()
    {
        $environment = new Environment();

        $processor = new FakeDelimiterProcessor();
        $environment->addDelimiterProcessor($processor);

        // Trigger initialization
        $environment->getBlockParsers();

        $this->assertSame($environment, $processor->getEnvironment());
        $this->assertNotNull($processor->getConfig());
    }

    public function testInjectableEventListenersGetInjected()
    {
        $environment = new Environment();

        $listener1 = new FakeEventListener(function () { });
        $listener2 = new FakeEventListenerInvokable(function () { });

        $environment->addEventListener('', [$listener1, 'doStuff']);
        $environment->addEventListener('', $listener2);

        // Trigger initialization
        $environment->getBlockParsers();

        $this->assertSame($environment, $listener1->getEnvironment());
        $this->assertSame($environment, $listener2->getEnvironment());

        $this->assertNotNull($listener1->getConfiguration());
        $this->assertNotNull($listener2->getConfiguration());
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

    public function testEventDispatching()
    {
        $environment = new Environment();
        $event = new FakeEvent();

        $actualOrder = [];

        $environment->addEventListener(FakeEvent::class, function (FakeEvent $e) use ($event, &$actualOrder) {
            $this->assertSame($event, $e);
            $actualOrder[] = 'a';
        });

        $environment->addEventListener(FakeEvent::class, function (FakeEvent $e) use ($event, &$actualOrder) {
            $this->assertSame($event, $e);
            $actualOrder[] = 'b';
            $e->stopPropagation();
        });

        $environment->addEventListener(FakeEvent::class, function (FakeEvent $e) use ($event, &$actualOrder) {
            $this->assertSame($event, $e);
            $actualOrder[] = 'c';
        }, 10);

        $environment->addEventListener(FakeEvent::class, function (FakeEvent $e) {
            $this->fail('Propogation should have been stopped before here');
        });

        $environment->dispatch($event);

        $this->assertCount(3, $actualOrder);
        $this->assertEquals('c', $actualOrder[0]);
        $this->assertEquals('a', $actualOrder[1]);
        $this->assertEquals('b', $actualOrder[2]);
    }

    public function testAddEventListenerFailsAfterInitialization()
    {
        $this->expectException(\RuntimeException::class);
        $environment = new Environment();
        $event = $this->createMock(AbstractEvent::class);

        $environment->dispatch($event);

        $environment->addEventListener(AbstractEvent::class, function (AbstractEvent $e) {
        });
    }

    private function assertFirstResult($expected, iterable $actual)
    {
        foreach ($actual as $a) {
            $this->assertSame($expected, $a);

            return;
        }

        $this->assertSame($expected, null);
    }
}
