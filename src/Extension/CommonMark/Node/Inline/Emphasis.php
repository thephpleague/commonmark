<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\CommonMark\Node\Inline;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\DelimitedInterface;

class Emphasis extends AbstractInline implements DelimitedInterface
{
    /** @var string */
    protected $delimeter;

    public function __construct(string $delimeter = '_')
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
