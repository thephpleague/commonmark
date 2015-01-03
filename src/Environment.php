<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
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

class Environment
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions = array();

    /**
     * @var MiscExtension
     */
    protected $miscExtension;

    /**
     * @var bool
     */
    protected $extensionsInitialized = false;

    /**
     * @var BlockParserInterface[]
     */
    protected $blockParsers = array();

    /**
     * @var BlockRendererInterface[]
     */
    protected $blockRenderersByClass = array();

    /**
     * @var InlineParserInterface[]
     */
    protected $inlineParsers = array();

    /**
     * @var array
     */
    protected $inlineParsersByCharacter = array();

    /**
     * @var InlineProcessorInterface[]
     */
    protected $inlineProcessors = array();

    /**
     * @var InlineRendererInterface[]
     */
    protected $inlineRenderersByClass = array();

    public function __construct()
    {
        $this->miscExtension = new MiscExtension();
    }

    /**
     * @param BlockParserInterface $parser
     *
     * @return $this
     */
    public function addBlockParser(BlockParserInterface $parser)
    {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException('Failed to add block parser - extensions have already been initialized');
        }

        $this->miscExtension->addBlockParser($parser);

        return $this;
    }

    /**
     * @param string $blockClass
     * @param BlockRendererInterface $blockRenderer
     *
     * @return $this
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer)
    {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException('Failed to add block renderer - extensions have already been initialized');
        }

        $this->miscExtension->addBlockRenderer($blockClass, $blockRenderer);

        return $this;
    }

    /**
     * @param InlineParserInterface $parser
     *
     * @return $this
     */
    public function addInlineParser(InlineParserInterface $parser)
    {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException('Failed to add inline parser - extensions have already been initialized');
        }

        $this->miscExtension->addInlineParser($parser);

        return $this;
    }

    /**
     * @param InlineProcessorInterface $processor
     *
     * @return $this
     */
    public function addInlineProcessor(InlineProcessorInterface $processor)
    {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException('Failed to add inline processor - extensions have already been initialized');
        }

        $this->miscExtension->addInlineProcessor($processor);

        return $this;
    }

    /**
     * @param string $inlineClass
     * @param InlineRendererInterface $renderer
     *
     * @return $this
     */
    public function addInlineRenderer($inlineClass, InlineRendererInterface $renderer)
    {
        if ($this->extensionsInitialized) {
            throw new \RuntimeException('Failed to add inline renderer - extensions have already been initialized');
        }

        $this->miscExtension->addInlineRenderer($inlineClass, $renderer);

        return $this;
    }

    /**
     * @return BlockParserInterface[]
     */
    public function getBlockParsers()
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->blockParsers;
    }

    /**
     * @param string $blockClass
     *
     * @return BlockRendererInterface|null
     */
    public function getBlockRendererForClass($blockClass)
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        if (!isset($this->blockRenderersByClass[$blockClass])) {
            return null;
        }

        return $this->blockRenderersByClass[$blockClass];
    }

    /**
     * @param string $name
     *
     * @return InlineParserInterface
     */
    public function getInlineParser($name)
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->inlineParsers[$name];
    }

    /**
     * @return InlineParserInterface[]
     */
    public function getInlineParsers()
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->inlineParsers;
    }

    /**
     * @param string $character
     *
     * @return InlineParserInterface[]|null
     */
    public function getInlineParsersForCharacter($character)
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        if (!isset($this->inlineParsersByCharacter[$character])) {
            return null;
        }

        return $this->inlineParsersByCharacter[$character];
    }

    /**
     * @return InlineProcessorInterface[]
     */
    public function getInlineProcessors()
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->inlineProcessors;
    }

    /**
     * @param string $inlineClass
     *
     * @return InlineRendererInterface|null
     */
    public function getInlineRendererForClass($inlineClass)
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        if (!isset($this->inlineRenderersByClass[$inlineClass])) {
            return null;
        }

        return $this->inlineRenderersByClass[$inlineClass];
    }

    public function createInlineParserEngine()
    {
        if (!$this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return new InlineParserEngine($this);
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
        if ($this->extensionsInitialized) {
            throw new \RuntimeException('Failed to add extension - extensions have already been initialized');
        }

        $this->extensions[$extension->getName()] = $extension;

        return $this;
    }

    protected function initializeExtensions()
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

        // Also initialize those one-off classes
        $this->initializeExtension($this->miscExtension);
    }

    /**
     * @param ExtensionInterface $extension
     */
    protected function initializeExtension(ExtensionInterface $extension)
    {
        // Block parsers
        foreach ($extension->getBlockParsers() as $blockParser) {
            if ($blockParser instanceof EnvironmentAwareInterface) {
                $blockParser->setEnvironment($this);
            }

            $this->blockParsers[$blockParser->getName()] = $blockParser;
        }

        // Block renderers
        foreach ($extension->getBlockRenderers() as $class => $blockRenderer) {
            $this->blockRenderersByClass[$class] = $blockRenderer;
        }

        // Inline parsers
        foreach ($extension->getInlineParsers() as $inlineParser) {
            if ($inlineParser instanceof EnvironmentAwareInterface) {
                $inlineParser->setEnvironment($this);
            }

            $this->inlineParsers[$inlineParser->getName()] = $inlineParser;

            foreach ($inlineParser->getCharacters() as $character) {
                $this->inlineParsersByCharacter[$character][] = $inlineParser;
            }
        }

        // Inline processors
        foreach ($extension->getInlineProcessors() as $inlineProcessor) {
            $this->inlineProcessors[] = $inlineProcessor;
        }

        // Inline renderers
        foreach ($extension->getInlineRenderers() as $class => $inlineRenderer) {
            $this->inlineRenderersByClass[$class] = $inlineRenderer;
        }
    }

    /**
     * @return Environment
     */
    public static function createCommonMarkEnvironment()
    {
        $environment = new static();
        $environment->addExtension(new CommonMarkCoreExtension());

        return $environment;
    }
}
