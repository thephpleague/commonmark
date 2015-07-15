<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension;

use League\CommonMark\Block\Parser as BlockParser;
use League\CommonMark\Block\Renderer as BlockRenderer;
use League\CommonMark\Inline\Parser as InlineParser;
use League\CommonMark\Inline\Processor as InlineProcessor;
use League\CommonMark\Inline\Renderer as InlineRenderer;

class SmartPunctExtension extends Extension
{
    /**
     * @return BlockParser\BlockParserInterface[]
     */
    public function getBlockParsers()
    {
        return [];
    }

    /**
     * @return BlockRenderer\BlockRendererInterface[]
     */
    public function getBlockRenderers()
    {
        return [
            'League\CommonMark\Block\Element\Document'  => new BlockRenderer\DocumentRenderer(),
            'League\CommonMark\Block\Element\Paragraph' => new BlockRenderer\ParagraphRenderer(),
        ];
    }

    /**
     * @return InlineParser\InlineParserInterface[]
     */
    public function getInlineParsers()
    {
        return [
            new InlineParser\QuoteParser(),
            new InlineParser\SmartPunctParser(),
        ];
    }

    /**
     * @return InlineProcessor\InlineProcessorInterface[]
     */
    public function getInlineProcessors()
    {
        return [
            new InlineProcessor\QuoteProcessor(),
        ];
    }

    /**
     * @return InlineRenderer\InlineRendererInterface[]
     */
    public function getInlineRenderers()
    {
        return [
            'League\CommonMark\Inline\Element\Text' => new InlineRenderer\TextRenderer(),
        ];
    }

    /**
     * Returns the name of the extension
     *
     * @return string
     */
    public function getName()
    {
        return 'smartpunct';
    }
}
