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

namespace League\CommonMark;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Extension\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\MiscExtension;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\Configuration;
use League\CommonMark\Util\ConfigurationAwareInterface;

final class Environment implements EnvironmentInterface, ConfigurableEnvironmentInterface
{
    /**
     * @var ExtensionInterface[]
     */
    private $extensions = [];

    /**
     * @var bool
     */
    private $extensionsInitialized = false;

    /**
     * @var BlockParserInterface[]
     */
    private $blockParsers = [];

    /**
     * @var InlineParserInterface[]
     */
    private $inlineParsers = [];

    /**
     * @var array
     */
    private $inlineParsersByCharacter = [];

    /**
     * @var DocumentProcessorInterface[]
     */
    private $documentProcessors = [];

    /**
     * @var InlineProcessorInterface[]
     */
    private $inlineProcessors = [];

    /**
     * @var BlockRendererInterface[]
     */
    private $blockRenderersByClass = [];

    /**
     * @var InlineRendererInterface[]
     */
    private $inlineRenderersByClass = [];

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var string
     */
    private $inlineParserCharacterRegex;

    public function __construct(array $config = [])
    {
        $this->config = new Configuration($config);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeConfig(array $config = [])
    {
        $this->assertUninitialized('Failed to modify configuration.');

        $this->config->mergeConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config = [])
    {
        $this->assertUninitialized('Failed to modify configuration.');

        $this->config->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($key = null, $default = null)
    {
        return $this->config->getConfig($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function addBlockParser(BlockParserInterface $parser)
    {
        $this->assertUninitialized('Failed to add block parser.');

        $this->getMiscExtension()->addBlockParser($parser);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addInlineParser(InlineParserInterface $parser)
    {
        $this->assertUninitialized('Failed to add inline parser.');

        $this->getMiscExtension()->addInlineParser($parser);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addInlineProcessor(InlineProcessorInterface $processor)
    {
        $this->assertUninitialized('Failed to add inline processor.');

        $this->getMiscExtension()->addInlineProcessor($processor);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addDocumentProcessor(DocumentProcessorInterface $processor)
    {
        $this->assertUninitialized('Failed to add document processor.');

        $this->getMiscExtension()->addDocumentProcessor($processor);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer)
    {
        $this->assertUninitialized('Failed to add block renderer.');

        $this->getMiscExtension()->addBlockRenderer($blockClass, $blockRenderer);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addInlineRenderer($inlineClass, InlineRendererInterface $renderer)
    {
        $this->assertUninitialized('Failed to add inline renderer.');

        $this->getMiscExtension()->addInlineRenderer($inlineClass, $renderer);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockParsers()
    {
        $this->initializeExtensions();

        return $this->blockParsers;
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineParsersForCharacter($character)
    {
        $this->initializeExtensions();

        if (!isset($this->inlineParsersByCharacter[$character])) {
            return [];
        }

        return $this->inlineParsersByCharacter[$character];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineProcessors()
    {
        $this->initializeExtensions();

        return $this->inlineProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentProcessors()
    {
        $this->initializeExtensions();

        return $this->documentProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockRendererForClass($blockClass)
    {
        $this->initializeExtensions();

        if (!isset($this->blockRenderersByClass[$blockClass])) {
            return;
        }

        return $this->blockRenderersByClass[$blockClass];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineRendererForClass($inlineClass)
    {
        $this->initializeExtensions();

        if (!isset($this->inlineRenderersByClass[$inlineClass])) {
            return;
        }

        return $this->inlineRenderersByClass[$inlineClass];
    }

    /**
     * Get all registered extensions
     *
     * @return ExtensionInterface[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Add a single extension
     *
     * @param ExtensionInterface $extension
     *
     * @return $this
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->assertUninitialized('Failed to add extension.');

        $this->extensions[] = $extension;

        return $this;
    }

    private function initializeExtensions()
    {
        // Only initialize them once
        if ($this->extensionsInitialized) {
            return;
        }

        $this->extensionsInitialized = true;

        // Initialize all the registered extensions
        foreach ($this->extensions as $extension) {
            $this->initializeExtension($extension);
        }

        // Lastly, let's build a regex which matches non-inline characters
        // This will enable a huge performance boost with inline parsing
        $this->buildInlineParserCharacterRegex();
    }

    /**
     * @param ExtensionInterface $extension
     */
    private function initializeExtension(ExtensionInterface $extension)
    {
        $this->initalizeBlockParsers($extension->getBlockParsers());
        $this->initializeInlineParsers($extension->getInlineParsers());
        $this->initializeInlineProcessors($extension->getInlineProcessors());
        $this->initializeDocumentProcessors($extension->getDocumentProcessors());
        $this->initializeBlockRenderers($extension->getBlockRenderers());
        $this->initializeInlineRenderers($extension->getInlineRenderers());
    }

    /**
     * @param BlockParserInterface[] $blockParsers
     */
    private function initalizeBlockParsers($blockParsers)
    {
        foreach ($blockParsers as $blockParser) {
            if ($blockParser instanceof EnvironmentAwareInterface) {
                $blockParser->setEnvironment($this);
            }

            if ($blockParser instanceof ConfigurationAwareInterface) {
                $blockParser->setConfiguration($this->config);
            }

            $this->blockParsers[$blockParser->getName()] = $blockParser;
        }
    }

    /**
     * @param InlineParserInterface[] $inlineParsers
     */
    private function initializeInlineParsers($inlineParsers)
    {
        foreach ($inlineParsers as $inlineParser) {
            if ($inlineParser instanceof EnvironmentAwareInterface) {
                $inlineParser->setEnvironment($this);
            }

            if ($inlineParser instanceof ConfigurationAwareInterface) {
                $inlineParser->setConfiguration($this->config);
            }

            $this->inlineParsers[$inlineParser->getName()] = $inlineParser;

            foreach ($inlineParser->getCharacters() as $character) {
                $this->inlineParsersByCharacter[$character][] = $inlineParser;
            }
        }
    }

    /**
     * @param InlineProcessorInterface[] $inlineProcessors
     */
    private function initializeInlineProcessors($inlineProcessors)
    {
        foreach ($inlineProcessors as $inlineProcessor) {
            $this->inlineProcessors[] = $inlineProcessor;

            if ($inlineProcessor instanceof ConfigurationAwareInterface) {
                $inlineProcessor->setConfiguration($this->config);
            }
        }
    }

    /**
     * @param DocumentProcessorInterface[] $documentProcessors
     */
    private function initializeDocumentProcessors($documentProcessors)
    {
        foreach ($documentProcessors as $documentProcessor) {
            $this->documentProcessors[] = $documentProcessor;

            if ($documentProcessor instanceof ConfigurationAwareInterface) {
                $documentProcessor->setConfiguration($this->config);
            }
        }
    }

    /**
     * @param BlockRendererInterface[] $blockRenderers
     */
    private function initializeBlockRenderers($blockRenderers)
    {
        foreach ($blockRenderers as $class => $blockRenderer) {
            $this->blockRenderersByClass[$class] = $blockRenderer;

            if ($blockRenderer instanceof ConfigurationAwareInterface) {
                $blockRenderer->setConfiguration($this->config);
            }
        }
    }

    /**
     * @param InlineRendererInterface[] $inlineRenderers
     */
    private function initializeInlineRenderers($inlineRenderers)
    {
        foreach ($inlineRenderers as $class => $inlineRenderer) {
            $this->inlineRenderersByClass[$class] = $inlineRenderer;

            if ($inlineRenderer instanceof ConfigurationAwareInterface) {
                $inlineRenderer->setConfiguration($this->config);
            }
        }
    }

    /**
     * @return Environment
     */
    public static function createCommonMarkEnvironment()
    {
        $environment = new static();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->mergeConfig([
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break'      => "\n",
            ],
            'safe'               => false, // deprecated option
            'html_input'         => self::HTML_INPUT_ALLOW,
            'allow_unsafe_links' => true,
            'max_nesting_level'  => INF,
        ]);

        return $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineParserCharacterRegex()
    {
        return $this->inlineParserCharacterRegex;
    }

    private function buildInlineParserCharacterRegex()
    {
        $chars = array_keys($this->inlineParsersByCharacter);

        if (empty($chars)) {
            // If no special inline characters exist then parse the whole line
            $this->inlineParserCharacterRegex = '/^.+$/u';
        } else {
            // Match any character which inline parsers are not interested in
            $this->inlineParserCharacterRegex = '/^[^' . preg_quote(implode('', $chars), '/') . ']+/u';
        }
    }

    /**
     * @param string $message
     *
     * @throws \RuntimeException
     */
    private function assertUninitialized($message)
    {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException($message . ' Extensions have already been initialized.');
        }
    }

    /**
     * @return MiscExtension
     */
    private function getMiscExtension()
    {
        $lastExtension = end($this->extensions);
        if ($lastExtension instanceof MiscExtension) {
            return $lastExtension;
        }

        $miscExtension = new MiscExtension();
        $this->addExtension($miscExtension);

        return $miscExtension;
    }
}
