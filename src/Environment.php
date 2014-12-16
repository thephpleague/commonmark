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
    public static function createCommonMarkEnvironment()
    {
        $environment = new static();

        $blockParsers = array(
            // This order is important
            new BlockParser\IndentedCodeParser(),
            new BlockParser\LazyParagraphParser(),
            new BlockParser\BlockQuoteParser(),
            new BlockParser\ATXHeaderParser(),
            new BlockParser\FencedCodeParser(),
            new BlockParser\HtmlBlockParser(),
            new BlockParser\SetExtHeaderParser(),
            new BlockParser\HorizontalRuleParser(),
            new BlockParser\ListParser(),
        );

        foreach ($blockParsers as $blockParser) {
            $environment->addBlockParser($blockParser);
        }

        $inlineParsers = array(
            new InlineParser\NewlineParser(),
            new InlineParser\BacktickParser(),
            new InlineParser\EscapableParser(),
            new InlineParser\EntityParser(),
            new InlineParser\EmphasisParser(),
            new InlineParser\AutolinkParser(),
            new InlineParser\RawHtmlParser(),
            new InlineParser\CloseBracketParser(),
            new InlineParser\OpenBracketParser(),
            new InlineParser\BangParser(),
        );

        foreach ($inlineParsers as $inlineParser) {
            $environment->addInlineParser($inlineParser);
        }

        $environment->addInlineProcessor(new EmphasisProcessor());

        $blockRenderers = array(
            'League\CommonMark\Block\Element\BlockQuote'          => new BlockRenderer\BlockQuoteRenderer(),
            'League\CommonMark\Block\Element\Document'            => new BlockRenderer\DocumentRenderer(),
            'League\CommonMark\Block\Element\FencedCode'          => new BlockRenderer\FencedCodeRenderer(),
            'League\CommonMark\Block\Element\Header'              => new BlockRenderer\HeaderRenderer(),
            'League\CommonMark\Block\Element\HorizontalRule'      => new BlockRenderer\HorizontalRuleRenderer(),
            'League\CommonMark\Block\Element\HtmlBlock'           => new BlockRenderer\HtmlBlockRenderer(),
            'League\CommonMark\Block\Element\IndentedCode'        => new BlockRenderer\IndentedCodeRenderer(),
            'League\CommonMark\Block\Element\ListBlock'           => new BlockRenderer\ListBlockRenderer(),
            'League\CommonMark\Block\Element\ListItem'            => new BlockRenderer\ListItemRenderer(),
            'League\CommonMark\Block\Element\Paragraph'           => new BlockRenderer\ParagraphRenderer(),
            'League\CommonMark\Block\Element\ReferenceDefinition' => new BlockRenderer\ReferenceDefinitionRenderer(),
        );

        foreach ($blockRenderers as $class => $renderer) {
            $environment->addBlockRenderer($class, $renderer);
        }

        $inlineRenderers = array(
            'League\CommonMark\Inline\Element\Code'     => new InlineRenderer\CodeRenderer(),
            'League\CommonMark\Inline\Element\Emphasis' => new InlineRenderer\EmphasisRenderer(),
            'League\CommonMark\Inline\Element\Html'     => new InlineRenderer\RawHtmlRenderer(),
            'League\CommonMark\Inline\Element\Image'    => new InlineRenderer\ImageRenderer(),
            'League\CommonMark\Inline\Element\Link'     => new InlineRenderer\LinkRenderer(),
            'League\CommonMark\Inline\Element\Newline'  => new InlineRenderer\NewlineRenderer(),
            'League\CommonMark\Inline\Element\Strong'   => new InlineRenderer\StrongRenderer(),
            'League\CommonMark\Inline\Element\Text'     => new InlineRenderer\TextRenderer(),
        );

        foreach ($inlineRenderers as $class => $renderer) {
            $environment->addInlineRenderer($class, $renderer);
        }

        return $environment;
    }
}
