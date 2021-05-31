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

namespace League\CommonMark\Extension\Emoji\Parser;

use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Extension\Emoji\Node\Emoji;
use League\CommonMark\Node\Inline\Text;
use UnicornFail\Emoji\Parser;
use UnicornFail\Emoji\Token\AbstractEmojiToken;

final class UnicornFailEmojiParser implements EmojiParserInterface
{
    /** @var ConfigurationInterface */
    private $config;

    /** @var Parser */
    private $parser;

    public function getParser(): Parser
    {
        if (! isset($this->parser)) {
            if (! \class_exists(Parser::class)) {
                throw new \RuntimeException('Failed to parse emojis: "unicorn-fail/emoji" library is missing');
            }

            $this->parser = new Parser($this->config->get('emoji/configuration', []));
        }

        return $this->parser;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $string): array
    {
        $nodes  = [];
        $tokens = $this->getParser()->parse($string);
        foreach ($tokens as $token) {
            $nodes[] = $token instanceof AbstractEmojiToken ? new Emoji($token) : new Text((string) $token);
        }

        return $nodes;
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
