<?php

/*
 * This file is part of the league/commonmark-ext-strikethrough package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Strikethrough;

use League\CommonMark\Extension\Extension;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

final class StrikethroughExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'strikethrough';
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineParsers()
    {
        return [
            new StrikethroughParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineRenderers()
    {
        return [
            Strikethrough::class => new StrikethroughRenderer(),
        ];
    }
}

