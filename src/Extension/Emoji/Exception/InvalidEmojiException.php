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

namespace League\CommonMark\Extension\Emoji\Exception;

final class InvalidEmojiException extends \RuntimeException
{
    public static function wrap(\Throwable $throwable): self
    {
        return new InvalidEmojiException('Failed to parse emojis: ' . $throwable->getMessage(), 0, $throwable);
    }
}
