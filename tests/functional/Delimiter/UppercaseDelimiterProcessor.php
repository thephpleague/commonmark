<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Delimiter;

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Inline\Element\AbstractStringContainer;

final class UppercaseDelimiterProcessor implements DelimiterProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOpeningCharacter(): string
    {
        return '{';
    }

    /**
     * {@inheritdoc}
     */
    public function getClosingCharacter(): string
    {
        return '}';
    }

    /**
     * {@inheritdoc}
     */
    public function getMinLength(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse)
    {
        $upperCase = new UppercaseText();
        $tmp = $opener->next();
        while ($tmp !== null && $tmp !== $closer) {
            $next = $tmp->next();
            $upperCase->appendChild($tmp);
            $tmp = $next;
        }
        $opener->insertAfter($upperCase);
    }
}
