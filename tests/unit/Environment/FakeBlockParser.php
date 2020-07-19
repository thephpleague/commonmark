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

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class FakeBlockParser extends AbstractFakeInjectable implements BlockParserInterface
{
    public function parse(ContextInterface $context, Cursor $cursor): bool
    {
        return false;
    }
}
