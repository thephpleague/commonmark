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

final class UrlAutolinkProcessor implements DocumentProcessorInterface
{
    // RegEx adapted from https://github.com/symfony/symfony/blob/4.2/src/Symfony/Component/Validator/Constraints/UrlValidator.php
    const REGEX = '~
        (?<=^|[ \\t\\n\\x0b\\x0c\\x0d*_\\~\\(])  # Can only come at the beginning of a line, after whitespace, or certain delimiting characters
        (
            # Must start with a supported scheme + auth, or "www"
            (?:
                (?:%s)://                                 # protocol
                (?:([\.\pL\pN-]+:)?([\.\pL\pN-]+)@)?      # basic auth
            |www\.)
            (?:
                (?:[\pL\pN\pS\-\.])+(?:\.?(?:[\pL\pN]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                    |                                                 # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                    # an IP address
                    |                                                 # or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # an IPv6 address
            )
            (?::[0-9]+)?                              # a port (optional)
            (?:/ (?:[\pL\pN\-._\~!$&\'()*+,;=:@]|%%[0-9A-Fa-f]{2})* )*      # a path
            (?:\? (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%%[0-9A-Fa-f]{2})* )?   # a query (optional)
            (?:\# (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%%[0-9A-Fa-f]{2})* )?   # a fragment (optional)
        )~ixu';

    private $allowedProtocols;

    public function __construct(array $allowedProtocols = ['http', 'https'])
    {
        $this->allowedProtocols = $allowedProtocols;
    }

    /**
     * @param Document $document
     *
     * @return void
     */
    public function processDocument(Document $document)
    {
        $regex = sprintf(self::REGEX, implode('|', $this->allowedProtocols));

        $walker = $document->walker();

        while ($event = $walker->next()) {
            if ($event->isEntering() && $event->getNode() instanceof Text) {
                /** @var Text $node */
                $node = $event->getNode();

                $contents = preg_split($regex, $node->getContent(), -1, PREG_SPLIT_DELIM_CAPTURE);

                $leftovers = '';
                foreach ($contents as $i => $content) {
                    if ($i % 2 === 0) {
                        $text = $leftovers.$content;
                        if ($text !== '') {
                            $node->insertBefore(new Text($leftovers . $content));
                        }
                        $leftovers = '';
                    } else {
                        // Does the URL end with punctuation that should be stripped?
                        if (preg_match('/(.+)([?!.,:*_~]+)$/', $content, $matches)) {
                            // Add the punctuation later
                            $content = $matches[1];
                            $leftovers = $matches[2];
                        }

                        // Does the URL need its closing paren chopped off?
                        if (substr($content, -1) === ')' && self::hasMoreCloserParensThanOpeners($content)) {
                            $content = substr($content, 0, -1);
                            $leftovers .= ')';
                        }

                        // Auto-prefix 'http://' onto 'www' URLs
                        if (substr($content, 0, 4) === 'www.') {
                            $node->insertBefore(new Link('http://'.$content, $content));
                        } else {
                            $node->insertBefore(new Link($content, $content));
                        }
                    }
                }

                $node->detach();
            }
        }
    }

    /**
     * @param string $content
     *
     * @return bool
     */
    private static function hasMoreCloserParensThanOpeners($content)
    {
        // Scan the entire autolink for the total number of parentheses.
        // If there is a greater number of closing parentheses than opening ones,
        // we don’t consider the last character part of the autolink, in order to
        // facilitate including an autolink inside a parenthesis.
        preg_match_all('/[()]/', $content, $matches);

        $charCount = ['(' => 0, ')' => 0];
        foreach ($matches[0] as $char) {
            $charCount[$char]++;
        }

        return $charCount[')'] > $charCount['('];
    }
}
