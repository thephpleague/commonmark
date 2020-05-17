<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Mention\LinkGenerator;

use League\CommonMark\Inline\Element\Link;

final class CallbackLinkGenerator implements MentionLinkGeneratorInterface
{
    /**
     * A callback function which returns the URL to use, or null if no link should be generated
     *
     * @var callable(string, string, string): ?string
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function generateLink(string $symbol, string $handle): ?Link
    {
        $label = $symbol . $handle;

        $url = \call_user_func_array($this->callback, [$handle, &$label, $symbol]);
        if ($url === null) {
            return null;
        }

        if (!\is_string($url)) {
            throw new \RuntimeException('CallbackLinkGenerator callable must return a string or null');
        }

        return new Link($url, $label);
    }
}
