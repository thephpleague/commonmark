<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Strikethrough;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\DelimitedInterface;

final class Strikethrough extends AbstractInline implements DelimitedInterface
{
    /** @var string */
    private $delimeter;

    public function __construct(string $delimeter = '~~')
    {
        parent::__construct();

        $this->delimeter = $delimeter;
    }

    public function getOpeningDelimiter(): string
    {
        return $this->delimeter;
    }

    public function getClosingDelimiter(): string
    {
        return $this->delimeter;
    }
}
