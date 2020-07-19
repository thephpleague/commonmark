<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Environment;

use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

class FakeInlineParser extends AbstractFakeInjectable implements InlineParserInterface
{
    public function getCharacters(): array
    {
        return [];
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        return false;
    }
}
