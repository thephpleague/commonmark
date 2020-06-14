<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Mention\Generator;

use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Inline\Element\AbstractInline;

final class CallbackGenerator implements MentionGeneratorInterface
{
    /**
     * A callback function which returns the URL to use, or null if no link should be generated
     *
     * @var callable(Mention): ?AbstractInline
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function generateMention(Mention $mention): ?AbstractInline
    {
        $value = \call_user_func_array($this->callback, [$mention]);
        if ($value === null && !$mention->hasUrl()) {
            return null;
        }

        if ($value instanceof AbstractInline && $value !== $mention) {
            return $value;
        }

        if ($mention->hasUrl()) {
            return $mention;
        }

        throw new \RuntimeException('CallbackGenerator callable must set the URL on the mention, return a new AbstractInline object or null if the mention cannot be generated');
    }
}
