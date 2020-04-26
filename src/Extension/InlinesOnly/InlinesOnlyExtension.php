<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\InlinesOnly;

use League\CommonMark as Core;
use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\CommonMark;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Extension\ExtensionInterface;

final class InlinesOnlyExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $childRenderer = new ChildRenderer();

        $environment
            ->addInlineParser(new Core\Parser\Inline\NewlineParser(),           200)
            ->addInlineParser(new CommonMark\Parser\Inline\BacktickParser(),    150)
            ->addInlineParser(new CommonMark\Parser\Inline\EscapableParser(),    80)
            ->addInlineParser(new CommonMark\Parser\Inline\EntityParser(),       70)
            ->addInlineParser(new CommonMark\Parser\Inline\AutolinkParser(),     50)
            ->addInlineParser(new CommonMark\Parser\Inline\HtmlInlineParser(),   40)
            ->addInlineParser(new CommonMark\Parser\Inline\CloseBracketParser(), 30)
            ->addInlineParser(new CommonMark\Parser\Inline\OpenBracketParser(),  20)
            ->addInlineParser(new CommonMark\Parser\Inline\BangParser(),         10)

            ->addBlockRenderer(Core\Node\Block\Document::class,  $childRenderer, 0)
            ->addBlockRenderer(Core\Node\Block\Paragraph::class, $childRenderer, 0)

            ->addInlineRenderer(CommonMark\Node\Inline\Code::class,       new CommonMark\Renderer\Inline\CodeRenderer(),       0)
            ->addInlineRenderer(CommonMark\Node\Inline\Emphasis::class,   new CommonMark\Renderer\Inline\EmphasisRenderer(),   0)
            ->addInlineRenderer(CommonMark\Node\Inline\HtmlInline::class, new CommonMark\Renderer\Inline\HtmlInlineRenderer(), 0)
            ->addInlineRenderer(CommonMark\Node\Inline\Image::class,      new CommonMark\Renderer\Inline\ImageRenderer(),      0)
            ->addInlineRenderer(CommonMark\Node\Inline\Link::class,       new CommonMark\Renderer\Inline\LinkRenderer(),       0)
            ->addInlineRenderer(Core\Node\Inline\Newline::class,          new Core\Renderer\Inline\NewlineRenderer(),          0)
            ->addInlineRenderer(CommonMark\Node\Inline\Strong::class,     new CommonMark\Renderer\Inline\StrongRenderer(),     0)
            ->addInlineRenderer(Core\Node\Inline\Text::class,             new Core\Renderer\Inline\TextRenderer(),             0)
        ;

        if ($environment->getConfig('use_asterisk', true)) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'));
        }
        if ($environment->getConfig('use_underscore', true)) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'));
        }
    }
}
