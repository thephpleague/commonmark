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

use League\CommonMark\Block\Parser as BlockParser;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer as BlockRenderer;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Environment\CommonMark;
use League\CommonMark\Environment\Markua;
use League\CommonMark\Inline\Parser as InlineParser;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\EmphasisProcessor;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer as InlineRenderer;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

class Environment
{
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

    /**
     * @param BlockParserInterface $parser
     *
     * @return $this
     */
    public function addBlockParser(BlockParserInterface $parser)
    {
        if ($parser instanceof EnvironmentAwareInterface) {
            $parser->setEnvironment($this);
        }

        $this->blockParsers[$parser->getName()] = $parser;

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
        $this->blockRenderersByClass[$blockClass] = $blockRenderer;

        return $this;
    }

    /**
     * @param InlineParserInterface $parser
     *
     * @return $this
     */
    public function addInlineParser(InlineParserInterface $parser)
    {
        if ($parser instanceof EnvironmentAwareInterface) {
            $parser->setEnvironment($this);
        }

        $this->inlineParsers[$parser->getName()] = $parser;

        foreach ($parser->getCharacters() as $character) {
            $this->inlineParsersByCharacter[$character][] = $parser;
        }

        return $this;
    }

    /**
     * @param InlineProcessorInterface $processor
     *
     * @return $this
     */
    public function addInlineProcessor(InlineProcessorInterface $processor)
    {
        $this->inlineProcessors[] = $processor;

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
        $this->inlineRenderersByClass[$inlineClass] = $renderer;

        return $this;
    }

    /**
     * @return BlockParserInterface[]
     */
    public function getBlockParsers()
    {
        return $this->blockParsers;
    }

    /**
     * @param string $blockClass
     *
     * @return BlockRendererInterface|null
     */
    public function getBlockRendererForClass($blockClass)
    {
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
        return $this->inlineParsers[$name];
    }

    /**
     * @return InlineParserInterface[]
     */
    public function getInlineParsers()
    {
        return $this->inlineParsers;
    }

    /**
     * @param string $character
     *
     * @return InlineParserInterface[]|null
     */
    public function getInlineParsersForCharacter($character)
    {
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
        return $this->inlineProcessors;
    }

    /**
     * @param string $inlineClass
     *
     * @return InlineRendererInterface|null
     */
    public function getInlineRendererForClass($inlineClass)
    {
        if (!isset($this->inlineRenderersByClass[$inlineClass])) {
            return null;
        }

        return $this->inlineRenderersByClass[$inlineClass];
    }

    public function createInlineParserEngine()
    {
        return new InlineParserEngine($this);
    }

    /**
     * @return Environment
     */
    public static function createEnvironment(EnvironmentInterface $type)
    {
        $environment = new static();

        foreach ($type->getBlockParsers() as $blockParser) {
            $environment->addBlockParser($blockParser);
        }

        foreach ($type->getInlineParsers() as $inlineParser) {
            $environment->addInlineParser($inlineParser);
        }

        foreach ($type->getInlineProcessors() as $inlineProcessor) {
            $environment->addInlineProcessor($inlineProcessor);
        }

        foreach ($type->getBlockRenderers() as $class => $renderer) {
            $environment->addBlockRenderer($class, $renderer);
        }

        foreach ($type->getInlineRenderers() as $class => $renderer) {
            $environment->addInlineRenderer($class, $renderer);
        }

        return $environment;
    }

    public static function createCommonMarkEnvironment()
    {
        return static::createEnvironment(new CommonMark());
    }

    public static function createMarkuaEnvironment()
    {
        return static::createEnvironment(new Markua());
    }
}
