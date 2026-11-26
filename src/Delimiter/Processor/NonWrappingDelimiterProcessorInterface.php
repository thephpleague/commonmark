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

namespace League\CommonMark\Delimiter\Processor;

/**
 * Marker for delimiter processors that never wrap or move the nodes between a matched
 * opener and closer (for example, smart quotes, which only replace the delimiters themselves)
 *
 * @internal
 */
interface NonWrappingDelimiterProcessorInterface extends DelimiterProcessorInterface
{
}
