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

namespace League\CommonMark\Extension\Emoji\Listener;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Emoji\Parser\EmojiParserInterface;
use League\CommonMark\Node\Inline\Text;

/**
 * Searches the Document for Text elements and parses it into distinct Emoji nodes.
 */
final class EmojiProcessorListener implements ConfigurationAwareInterface
{
    /**
     * @var EmojiParserInterface
     *
     * @psalm-readonly
     */
    private $parser;

    public function __construct(EmojiParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function __invoke(DocumentParsedEvent $e): void
    {
        $walker = $e->getDocument()->walker();
        while ($event = $walker->next()) {
            if (! $event->isEntering()) {
                continue;
            }

            $text = $event->getNode();
            if (! ($text instanceof Text)) {
                continue;
            }

            $nodes = $this->parser->parse($text->getLiteral());
            if (! $nodes) {
                continue;
            }

            foreach ($nodes as $node) {
                $text->insertBefore($node);
            }

            $text->detach();
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->parser->setConfiguration($configuration);
    }
}
