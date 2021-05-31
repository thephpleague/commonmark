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

namespace League\CommonMark\Extension\Emoji\Node;

use League\CommonMark\Node\Inline\AbstractInline;
use UnicornFail\Emoji\Token\AbstractEmojiToken;

final class Emoji extends AbstractInline
{
    /** @var AbstractEmojiToken */
    protected $token;

    public function __construct(AbstractEmojiToken $token)
    {
        $this->token = $token;
    }

    public function getToken(): ?AbstractEmojiToken
    {
        return $this->token;
    }
}
