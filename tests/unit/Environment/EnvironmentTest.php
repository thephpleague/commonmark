<?php

declare(strict_types=1);

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

namespace League\CommonMark\Tests\Unit\Environment;

use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\AbstractEvent;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Exception\AlreadyInitializedException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Normalizer\TextNormalizerInterface;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Block\SkipLinesStartingWithLettersParser;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Tests\Unit\Event\FakeEvent;
use League\CommonMark\Tests\Unit\Event\FakeEventListener;
use League\CommonMark\Tests\Unit\Event\FakeEventListenerInvokable;
use League\CommonMark\Tests\Unit\Event\FakeEventParent;
use League\CommonMark\Util\ArrayCollection;
use League\CommonMark\Util\HtmlFilter;
use League\Config\ConfigurationBuilderInterface;
use League\Config\ConfigurationInterface;
use League\Config\MutableConfigurationInterface;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

final class EnvironmentTest extends TestCase
{
    public function testAddGetExtensions(): void
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
        $environment->getBlockStartParsers();
    }

    public function testConstructor(): void
    {
        $config      = ['max_nesting_level' => 42];
        $environment = new Environment($config);
        $this->assertSame(42, $environment->getConfiguration()->get('max_nesting_level'));
    }

    public function testGetConfiguration(): void
    {
        $config      = ['max_nesting_level' => 3];
        $environment = new Environment($config);

        $configuration = $environment->getConfiguration();
        $this->assertInstanceOf(ConfigurationInterface::class, $configuration);
        $this->assertNotInstanceOf(MutableConfigurationInterface::class, $configuration);
        $this->assertSame(3, $configuration->get('max_nesting_level'));
    }

    public function testMergeConfig(): void
    {
        $environment = $this->createEnvironmentWithSchema([
            'foo' => Expect::string(),
            'test' => Expect::string(),
        ]);

        $environment->mergeConfig(['foo' => 'foo']);

        $this->assertEquals('foo', $environment->getConfiguration()->get('foo'));
        $this->assertNull($environment->getConfiguration()->get('test'));

        $environment->mergeConfig(['test' => '123', 'foo' => 'bar']);

        $this->assertEquals('bar', $environment->getConfiguration()->get('foo'));
        $this->assertEquals('123', $environment->getConfiguration()->get('test'));

        $environment->mergeConfig(['test' => '456']);

        $this->assertEquals('bar', $environment->getConfiguration()->get('foo'));
        $this->assertEquals('456', $environment->getConfiguration()->get('test'));
    }

    public function testMergeConfigAfterInit(): void
    {
        $this->expectException(AlreadyInitializedException::class);

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockStartParsers();
        $environment->mergeConfig(['foo' => 'bar']);
    }

    public function testAddBlockStartParserAndGetter(): void
    {
        $environment = new Environment();

        $parser = $this->createMock(BlockStartParserInterface::class);
        $environment->addBlockStartParser($parser);

        $this->assertContains($parser, $environment->getBlockStartParsers());
    }

    public function testAddBlockStartParserFailsAfterInitialization(): void
    {
        $this->expectException(AlreadyInitializedException::class);

        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockStartParsers();

        $parser = $this->createMock(BlockStartParserInterface::class);
        $environment->addBlockStartParser($parser);
    }

    public function testAddRenderer(): void
    {
        $environment = new Environment();

        $renderer = $this->createMock(NodeRendererInterface::class);
        $environment->addRenderer('MyClass', $renderer);

        $this->assertContains($renderer, $environment->getRenderersForClass('MyClass'));
    }

    public function testAddRendererFailsAfterInitialization(): void
    {
        $this->expectException(AlreadyInitializedException::class);

        $environment = new Environment();

        // This triggers the initialization
        $environment->getRenderersForClass('MyClass');

        $renderer = $this->createMock(NodeRendererInterface::class);
        $environment->addRenderer('MyClass', $renderer);
    }

    public function testAddInlineParserFailsAfterInitialization(): void
    {
        $this->expectException(AlreadyInitializedException::class);

        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineParsers();

        $parser = $this->createMock(InlineParserInterface::class);
        $environment->addInlineParser($parser);
    }

    public function testAddDelimiterProcessor(): void
    {
        $environment = new Environment();

        $processor = $this->createMock(DelimiterProcessorInterface::class);
        $processor->method('getOpeningCharacter')->willReturn('*');
        $environment->addDelimiterProcessor($processor);

        $this->assertSame($processor, $environment->getDelimiterProcessors()->getDelimiterProcessor('*'));
    }

    public function testAddDelimiterProcessorFailsAfterInitialization(): void
    {
        $this->expectException(AlreadyInitializedException::class);

        $environment = new Environment();

        // This triggers the initialization
        $environment->getDelimiterProcessors();

        $processor = $this->createMock(DelimiterProcessorInterface::class);
        $environment->addDelimiterProcessor($processor);
    }

    public function testGetRendererForUnknownClass(): void
    {
        $environment  = new Environment();
        $mockRenderer = $this->createMock(NodeRendererInterface::class);
        $environment->addRenderer(FakeBlock3::class, $mockRenderer);

        $this->assertEmpty($environment->getRenderersForClass(FakeBlock1::class));
    }

    public function testGetRendererForSubClass(): void
    {
        $environment  = new Environment();
        $mockRenderer = $this->createMock(NodeRendererInterface::class);
        $environment->addRenderer(FakeBlock1::class, $mockRenderer);

        // Ensure the parent renderer is returned
        $this->assertFirstResult($mockRenderer, $environment->getRenderersForClass(FakeBlock3::class));
        // Check again to ensure any cached result is also the same
        $this->assertFirstResult($mockRenderer, $environment->getRenderersForClass(FakeBlock3::class));
    }

    public function testAddExtensionAndGetter(): void
    {
        $environment = new Environment();

        $extension = $this->createMock(ExtensionInterface::class);
        $environment->addExtension($extension);

        $this->assertContains($extension, $environment->getExtensions());
    }

    public function testAddExtensionFailsAfterInitialization(): void
    {
        $this->expectException(AlreadyInitializedException::class);

        $environment = new Environment();

        // This triggers the initialization
        $environment->getRenderersForClass('MyClass');

        $extension = $this->createMock(ExtensionInterface::class);
        $environment->addExtension($extension);
    }

    public function testInjectableBlockStartParsersGetInjected(): void
    {
        $environment = new Environment();

        $parser = new FakeInjectableBlockStartParser();
        $environment->addBlockStartParser($parser);

        // Trigger initialization
        $environment->getBlockStartParsers();

        $this->assertTrue($parser->bothWereInjected());
    }

    public function testInjectableRenderersGetInjected(): void
    {
        $environment = new Environment();

        $renderer = new FakeInjectableRenderer();
        $environment->addRenderer('', $renderer);

        // Trigger initialization
        $environment->getBlockStartParsers();

        $this->assertTrue($renderer->bothWereInjected());
    }

    public function testInjectableInlineParsersGetInjected(): void
    {
        $environment = new Environment();

        $parser = new FakeInjectableInlineParser();
        $environment->addInlineParser($parser);

        // Trigger initialization
        $environment->getBlockStartParsers();

        $this->assertTrue($parser->bothWereInjected());
    }

    public function testInjectableDelimiterProcessorsGetInjected(): void
    {
        $environment = new Environment();

        $processor = new FakeInjectableDelimiterProcessor();
        $environment->addDelimiterProcessor($processor);

        // Trigger initialization
        $environment->getBlockStartParsers();

        $this->assertTrue($processor->bothWereInjected());
    }

    public function testInjectableEventListenersGetInjected(): void
    {
        $environment = new Environment();

        // phpcs:ignore Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
        $listener1 = new FakeEventListener(static function (): void { });
        // phpcs:ignore Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
        $listener2 = new FakeEventListenerInvokable(static function (): void { });

        $environment->addEventListener('', [$listener1, 'doStuff']);
        $environment->addEventListener('', $listener2);

        // Trigger initialization
        $environment->getBlockStartParsers();

        $this->assertSame($environment, $listener1->getEnvironment());
        $this->assertSame($environment, $listener2->getEnvironment());

        $this->assertNotNull($listener1->getConfiguration());
        $this->assertNotNull($listener2->getConfiguration());
    }

    public function testSkipLinesParserIncludedByDefault(): void
    {
        $environment = new Environment();

        $parsers = \iterator_to_array($environment->getBlockStartParsers());

        $this->assertCount(1, $parsers);
        $this->assertInstanceOf(SkipLinesStartingWithLettersParser::class, $parsers[0]);
    }

    public function testBlockParserPrioritization(): void
    {
        $environment = new Environment();

        $parser1 = $this->createMock(BlockStartParserInterface::class);
        $parser2 = $this->createMock(BlockStartParserInterface::class);
        $parser3 = $this->createMock(BlockStartParserInterface::class);

        $environment->addBlockStartParser($parser1);
        $environment->addBlockStartParser($parser2, 500);
        $environment->addBlockStartParser($parser3);

        $parsers = \iterator_to_array($environment->getBlockStartParsers());

        $this->assertSame($parser2, $parsers[0]);
        $this->assertInstanceOf(SkipLinesStartingWithLettersParser::class, $parsers[1]);
        $this->assertSame($parser1, $parsers[2]);
        $this->assertSame($parser3, $parsers[3]);
    }

    public function testGetInlineParsersWithPrioritization(): void
    {
        $environment = new Environment();

        $parser1 = $this->createMock(InlineParserInterface::class);
        $parser2 = $this->createMock(InlineParserInterface::class);
        $parser3 = $this->createMock(InlineParserInterface::class);

        $environment->addInlineParser($parser1);
        $environment->addInlineParser($parser2, 50);
        $environment->addInlineParser($parser3);

        $parsers = \iterator_to_array($environment->getInlineParsers());

        $this->assertSame($parser2, $parsers[0]);
        $this->assertSame($parser1, $parsers[1]);
        $this->assertSame($parser3, $parsers[2]);
    }

    public function testRendererPrioritization(): void
    {
        $environment = new Environment();

        $renderer1 = $this->createMock(NodeRendererInterface::class);
        $renderer2 = $this->createMock(NodeRendererInterface::class);
        $renderer3 = $this->createMock(NodeRendererInterface::class);

        $environment->addRenderer('foo', $renderer1);
        $environment->addRenderer('foo', $renderer2, 50);
        $environment->addRenderer('foo', $renderer3);

        $parsers = \iterator_to_array($environment->getRenderersForClass('foo'));

        $this->assertSame($renderer2, $parsers[0]);
        $this->assertSame($renderer1, $parsers[1]);
        $this->assertSame($renderer3, $parsers[2]);
    }

    public function testEventDispatching(): void
    {
        $environment = new Environment();
        $event       = new FakeEvent();

        $actualOrder = [];

        $environment->addEventListener(FakeEvent::class, function (FakeEvent $e) use ($event, &$actualOrder): void {
            $this->assertSame($event, $e);
            $actualOrder[] = 'a';
        });

        // Listeners on parent classes should also be called
        $environment->addEventListener(FakeEventParent::class, function (FakeEvent $e) use ($event, &$actualOrder): void {
            $this->assertSame($event, $e);
            $actualOrder[] = 'b';
            $e->stopPropagation();
        });

        $environment->addEventListener(FakeEvent::class, function (FakeEvent $e) use ($event, &$actualOrder): void {
            $this->assertSame($event, $e);
            $actualOrder[] = 'c';
        }, 10);

        $environment->addEventListener(FakeEvent::class, function (FakeEvent $e): void {
            $this->fail('Propogation should have been stopped before here');
        });

        $environment->dispatch($event);

        $this->assertCount(3, $actualOrder);
        $this->assertEquals('c', $actualOrder[0]);
        $this->assertEquals('a', $actualOrder[1]);
        $this->assertEquals('b', $actualOrder[2]);
    }

    public function testAddEventListenerFailsAfterInitialization(): void
    {
        $this->expectException(AlreadyInitializedException::class);

        $environment = new Environment();

        // Trigger initialization
        $environment->dispatch($this->createMock(AbstractEvent::class));

        $environment->addEventListener(AbstractEvent::class, static function (AbstractEvent $e): void {
        });
    }

    public function testDispatchDelegatesToProvidedDispatcher(): void
    {
        $dispatchersCalled = new ArrayCollection();

        $environment = new Environment();

        $environment->addEventListener(FakeEvent::class, static function (FakeEvent $event) use ($dispatchersCalled): void {
            $dispatchersCalled[] = 'THIS SHOULD NOT BE CALLED!';
        });

        $environment->setEventDispatcher(new class ($dispatchersCalled) implements EventDispatcherInterface {
            private ArrayCollection $dispatchersCalled;

            public function __construct(ArrayCollection $dispatchersCalled)
            {
                $this->dispatchersCalled = $dispatchersCalled;
            }

            public function dispatch(object $event): object
            {
                $this->dispatchersCalled[] = 'external';

                return $event;
            }
        });

        $environment->dispatch(new FakeEvent());

        $this->assertCount(1, $dispatchersCalled);
        $this->assertSame('external', $dispatchersCalled->first());
    }

    public function testGetDefaultSlugNormalizer(): void
    {
        $environment = new Environment();
        $normalizer  = $environment->getSlugNormalizer();

        $this->assertSame('test', $normalizer->normalize('Test'));
        $this->assertSame('test-1', $normalizer->normalize('Test'));
    }

    public function testCustomSlugNormalizer(): void
    {
        $innerNormalizer = $this->createStub(TextNormalizerInterface::class);
        $innerNormalizer->method('normalize')->willReturn('foo');

        $environment = new Environment([
            'slug_normalizer' => [
                'instance' => $innerNormalizer,
            ],
        ]);

        $normalizer = $environment->getSlugNormalizer();
        $this->assertSame('foo', $normalizer->normalize('Foo'));
        $this->assertSame('foo-1', $normalizer->normalize('Foo'));
    }

    public function testUniqueSlugNormalizerDisabled(): void
    {
        $environment = new Environment([
            'slug_normalizer' => [
                'unique' => false,
            ],
        ]);

        $normalizer = $environment->getSlugNormalizer();
        $this->assertSame('foo', $normalizer->normalize('Foo'));
        $this->assertSame('foo', $normalizer->normalize('Foo'));
        $this->assertSame('foo', $normalizer->normalize('Foo'));
    }

    public function testUniqueSlugNormalizerPerDocument(): void
    {
        $environment = new Environment([
            'slug_normalizer' => [
                'unique' => 'document',
            ],
        ]);

        $normalizer = $environment->getSlugNormalizer();
        $this->assertSame('foo', $normalizer->normalize('Foo'));
        $this->assertSame('foo-1', $normalizer->normalize('Foo'));
        $this->assertSame('foo-2', $normalizer->normalize('Foo'));

        $environment->dispatch(new DocumentParsedEvent(new Document()));

        $this->assertSame('foo', $normalizer->normalize('Foo'));
        $this->assertSame('foo-1', $normalizer->normalize('Foo'));
        $this->assertSame('foo-2', $normalizer->normalize('Foo'));
    }

    public function testUniqueSlugNormalizerPerEnvironment(): void
    {
        $environment = new Environment([
            'slug_normalizer' => [
                'unique' => 'environment',
            ],
        ]);

        $normalizer = $environment->getSlugNormalizer();
        $this->assertSame('foo', $normalizer->normalize('Foo'));
        $this->assertSame('foo-1', $normalizer->normalize('Foo'));
        $this->assertSame('foo-2', $normalizer->normalize('Foo'));

        $environment->dispatch(new DocumentParsedEvent(new Document()));

        $this->assertSame('foo-3', $normalizer->normalize('Foo'));
        $this->assertSame('foo-4', $normalizer->normalize('Foo'));
        $this->assertSame('foo-5', $normalizer->normalize('Foo'));
    }

    /**
     * @param mixed           $expected
     * @param iterable<mixed> $actual
     */
    private function assertFirstResult($expected, iterable $actual): void
    {
        foreach ($actual as $a) {
            $this->assertSame($expected, $a);

            return;
        }

        $this->assertSame($expected, null);
    }

    /**
     * @param array<string, Schema> $schemas
     */
    private function createEnvironmentWithSchema(array $schemas): Environment
    {
        $environment = new Environment();
        $environment->addExtension(new class ($schemas) implements ConfigurableExtensionInterface {
            /** @var array<string, Schema> */
            private array $schemas;

            /**
             * @param array<string, Schema> $schemas
             */
            public function __construct(array $schemas)
            {
                $this->schemas = $schemas;
            }

            public function configureSchema(ConfigurationBuilderInterface $builder): void
            {
                foreach ($this->schemas as $key => $schema) {
                    $builder->addSchema($key, $schema);
                }
            }

            public function register(EnvironmentBuilderInterface $environment): void
            {
            }
        });

        return $environment;
    }

    public function testCreateCommonMarkEnvironment(): void
    {
        $environment = Environment::createCommonMarkEnvironment(['html_input' => HtmlFilter::ESCAPE]);

        $this->assertCount(1, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);

        $this->assertSame(HtmlFilter::ESCAPE, $environment->getConfiguration()->get('html_input'));
    }

    public function testCreateGFMEnvironment(): void
    {
        $environment = Environment::createGFMEnvironment(['html_input' => HtmlFilter::ESCAPE]);

        $this->assertCount(2, $environment->getExtensions());
        $this->assertInstanceOf(CommonMarkCoreExtension::class, $environment->getExtensions()[0]);
        $this->assertInstanceOf(GithubFlavoredMarkdownExtension::class, $environment->getExtensions()[1]);

        $this->assertSame(HtmlFilter::ESCAPE, $environment->getConfiguration()->get('html_input'));
    }
}
