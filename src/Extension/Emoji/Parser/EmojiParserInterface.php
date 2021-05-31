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

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Extension\Emoji\Exception\InvalidEmojiException;
use League\CommonMark\Node\Inline\AbstractInline;

interface EmojiParserInterface extends ConfigurationAwareInterface
{
    /**
     * @return AbstractInline[]
     *
     * @throws InvalidEmojiException if parsing fails
     * @throws \RuntimeException if other errors occur
     */
    public function parse(string $string): array;
}
