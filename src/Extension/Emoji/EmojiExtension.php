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

namespace League\CommonMark\Extension\Emoji;

use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Emoji\Listener\EmojiProcessorListener;
use League\CommonMark\Extension\Emoji\Node\Emoji;
use League\CommonMark\Extension\Emoji\Parser\EmojiParserInterface;
use League\CommonMark\Extension\Emoji\Parser\UnicornFailEmojiParser;
use League\CommonMark\Extension\ExtensionInterface;

final class EmojiExtension implements ExtensionInterface
{
    /**
     * @var EmojiParserInterface
     *
     * @psalm-readonly
     */
    private $parser;

    public function __construct(?EmojiParserInterface $parser = null)
    {
        $this->parser = $parser ?? new UnicornFailEmojiParser();
    }

    public function getEmojiParser(): EmojiParserInterface
    {
        return $this->parser;
    }

    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, new EmojiProcessorListener($this->parser), -100);
        $environment->addRenderer(Emoji::class, new EmojiRenderer());
    }
}
