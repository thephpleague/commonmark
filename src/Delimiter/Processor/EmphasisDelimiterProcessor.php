<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter\Processor;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Strong;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\Configuration;

final class EmphasisDelimiterProcessor implements DelimiterProcessorInterface
{
    private $char;

    /**
     * @param string $char The emphasis character to use (typically '*' or '_')
     */
    public function __construct(string $char)
    {
        $this->char = $char;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharacter(): string
    {
        return $this->char;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelimiterUse(Delimiter $opener, Delimiter $closer): int
    {
        // "Multiple of 3" rule for internal delimiter runs
        if (($opener->canClose() || $closer->canOpen()) && $closer->getOrigDelims() % 3 !== 0 && ($opener->getOrigDelims() + $closer->getOrigDelims()) % 3 === 0) {
            return 0;
        }

        // Calculate actual number of delimiters used from this closer
        if ($opener->getNumDelims() >= 2 && $closer->getNumDelims() >= 2) {
            return 2;
        }

        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Text $opener, Text $closer, int $delimiterUse)
    {
        /** @var Configuration|null $emphasisConfig */
        $emphasisConfig = $opener->getData('emphasis_config');

        if ($delimiterUse === 1 && ($emphasisConfig === null || $emphasisConfig->getConfig('enable_em', true))) {
            $emphasis = new Emphasis();
        } elseif ($delimiterUse === 2 && ($emphasisConfig === null || $emphasisConfig->getConfig('enable_strong', true))) {
            $emphasis = new Strong();
        } else {
            return;
        }

        $tmp = $opener->next();
        while ($tmp !== null && $tmp !== $closer) {
            $next = $tmp->next();
            $emphasis->appendChild($tmp);
            $tmp = $next;
        }

        $opener->insertAfter($emphasis);
    }
}
