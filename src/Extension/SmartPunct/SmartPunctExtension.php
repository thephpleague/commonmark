<?php

declare(strict_types=1);

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

namespace League\CommonMark\Extension\SmartPunct;

use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\Block as CoreBlockRenderer;
use League\CommonMark\Renderer\Inline as CoreInlineRenderer;

final class SmartPunctExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $environment
            ->addInlineParser(new QuoteParser(), 10)
            ->addInlineParser(new DashParser(), 0)
            ->addInlineParser(new EllipsesParser(), 0)

            ->addDelimiterProcessor(QuoteProcessor::createDoubleQuoteProcessor(
                $environment->getConfig('smartpunct/double_quote_opener', Quote::DOUBLE_QUOTE_OPENER),
                $environment->getConfig('smartpunct/double_quote_closer', Quote::DOUBLE_QUOTE_CLOSER)
            ))
            ->addDelimiterProcessor(QuoteProcessor::createSingleQuoteProcessor(
                $environment->getConfig('smartpunct/single_quote_opener', Quote::SINGLE_QUOTE_OPENER),
                $environment->getConfig('smartpunct/single_quote_closer', Quote::SINGLE_QUOTE_CLOSER)
            ))

            ->addRenderer(Document::class, new CoreBlockRenderer\DocumentRenderer(), 0)
            ->addRenderer(Paragraph::class, new CoreBlockRenderer\ParagraphRenderer(), 0)

            ->addRenderer(Quote::class, new QuoteRenderer(), 100)
            ->addRenderer(Text::class, new CoreInlineRenderer\TextRenderer(), 0);
    }
}
