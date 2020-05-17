<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\HeadingPermalink\SlugGenerator;

use League\CommonMark\Inline\Element\Code;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Node\Node;

/**
 * Creates URL-friendly strings based on the inner contents of a node and its descendants
 */
final class DefaultSlugGenerator implements SlugGeneratorInterface
{
    public function generateSlug(Node $node): string
    {
        $childText = $this->getChildText($node);

        return self::slugifyText($childText);
    }

    /**
     * @internal This method is only public to facilitate internal testing. DO NOT RELY ON ITS EXISTENCE OR BEHAVIOR!
     */
    public static function slugifyText(string $text): string
    {
        // Trim whitespace
        $slug = \trim($text);
        // Convert to lowercase
        $slug = \mb_strtolower($slug);
        // Try replacing whitespace with a dash
        $slug = \preg_replace('/\s+/u', '-', $slug) ?? $slug;
        // Try removing characters other than letters, numbers, and marks.
        $slug = \preg_replace('/[^\p{L}\p{Nd}\p{Nl}\p{M}-]+/u', '', $slug) ?? $slug;

        return $slug;
    }

    /**
     * @deprecated Use StringContainerHelper::getChildText() in 2.0
     */
    private function getChildText(Node $node): string
    {
        $text = '';

        $walker = $node->walker();
        while ($event = $walker->next()) {
            if ($event->isEntering() && (($child = $event->getNode()) instanceof Text || $child instanceof Code)) {
                $text .= $child->getContent();
            }
        }

        return $text;
    }
}
