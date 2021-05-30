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

namespace League\CommonMark\Tests\Unit\Environment;

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Node\Inline\AbstractStringContainer;
use League\Config\ConfigurationAwareInterface;

final class FakeInjectableDelimiterProcessor implements DelimiterProcessorInterface, ConfigurationAwareInterface, EnvironmentAwareInterface
{
    use FakeInjectableTrait;

    public function getOpeningCharacter(): string
    {
        return 'x';
    }

    public function getClosingCharacter(): string
    {
        return 'x';
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        return 0;
    }

    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void
    {
    }
}
