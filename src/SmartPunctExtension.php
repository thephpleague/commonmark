<?php

/*
 * This file is part of the league/commonmark-extras package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\SmartPunct;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer as CoreBlockRenderer;
use League\CommonMark\Extension\Extension;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Renderer as CoreInlineRenderer;

class SmartPunctExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getInlineParsers()
    {
        return [
            new QuoteParser(),
            new PunctuationParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineProcessors()
    {
        return [
            new QuoteProcessor(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockRenderers()
    {
        return [
            Document::class  => new CoreBlockRenderer\DocumentRenderer(),
            Paragraph::class => new CoreBlockRenderer\ParagraphRenderer(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineRenderers()
    {
        return [
            Text::class => new CoreInlineRenderer\TextRenderer(),
        ];
    }
}
