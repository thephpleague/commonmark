<?php

/*
 * This file is part of the league/commonmark-ext-autolink package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Autolink;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\DocumentProcessorInterface;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;

final class EmailAutolinkProcessor implements DocumentProcessorInterface
{
    // RegEx adapted from https://github.com/symfony/symfony/blob/4.2/src/Symfony/Component/Validator/Constraints/EmailValidator.php
    const REGEX = '/([a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+)/';

    /**
     * @param Document $document
     *
     * @return void
     */
    public function processDocument(Document $document)
    {
        $walker = $document->walker();

        while ($event = $walker->next()) {
            if ($event->isEntering() && $event->getNode() instanceof Text) {
                /** @var Text $node */
                $node = $event->getNode();

                $contents = preg_split(self::REGEX, $node->getContent(), -1, PREG_SPLIT_DELIM_CAPTURE);
                foreach ($contents as $i => $content) {
                    if ($i % 2 === 0) {
                        $node->insertBefore(new Text($content));
                    } else {
                        $node->insertBefore(new Link('mailto:'.$content, $content));
                    }
                }

                $node->detach();
            }
        }
    }
}
