<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\TableOfContents;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsPlaceholder;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

final class TableOfContentsPlaceholderParser extends AbstractBlockContinueParser
{
    /**
     * @var TableOfContentsPlaceholder
     *
     * @psalm-readonly
     */
    private $block;

    public function __construct()
    {
        $this->block = new TableOfContentsPlaceholder();
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        return BlockContinue::none();
    }

    public static function blockStartParser(): BlockStartParserInterface
    {
        return new class () implements BlockStartParserInterface, ConfigurationAwareInterface {
            /**
             * @var ConfigurationInterface
             *
             * @psalm-readonly-allow-private-mutation
             */
            private $config;

            public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
            {
                $placeholder = $this->config->get('table_of_contents/placeholder');
                if ($placeholder === null) {
                    return BlockStart::none();
                }

                // The placeholder must be the only thing on the line
                if ($cursor->match('/^' . \preg_quote($placeholder, '/') . '$/') === null) {
                    return BlockStart::none();
                }

                return BlockStart::of(new TableOfContentsPlaceholderParser())->at($cursor);
            }

            public function setConfiguration(ConfigurationInterface $configuration): void
            {
                $this->config = $configuration;
            }
        };
    }
}
