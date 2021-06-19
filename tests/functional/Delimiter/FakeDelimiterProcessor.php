<?php

declare(strict_types=1);

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
use League\CommonMark\Node\Inline\AbstractStringContainer;

final class FakeDelimiterProcessor implements DelimiterProcessorInterface
{
    private string $delimiterChar;

    private int $delimiterUse;

    public function __construct(string $delimiterChar, int $delimiterUse)
    {
        $this->delimiterChar = $delimiterChar;
        $this->delimiterUse  = $delimiterUse;
    }

    public function getOpeningCharacter(): string
    {
        return $this->delimiterChar;
    }

    public function getClosingCharacter(): string
    {
        return $this->delimiterChar;
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        return $this->delimiterUse;
    }

    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void
    {
    }
}
