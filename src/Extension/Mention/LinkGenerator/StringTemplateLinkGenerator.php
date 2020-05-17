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

namespace League\CommonMark\Extension\Mention\LinkGenerator;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;

final class StringTemplateLinkGenerator implements MentionLinkGeneratorInterface
{
    /** @var string */
    private $urlTemplate;

    public function __construct(string $urlTemplate)
    {
        $this->urlTemplate = $urlTemplate;
    }

    public function generateLink(string $symbol, string $handle): ?Link
    {
        $url = \sprintf($this->urlTemplate, $handle);

        return new Link($url, $symbol . $handle);
    }
}
