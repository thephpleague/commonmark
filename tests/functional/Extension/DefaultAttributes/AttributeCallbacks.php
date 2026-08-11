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

namespace League\CommonMark\Tests\Functional\Extension\DefaultAttributes;

use League\CommonMark\Node\Node;
use League\CommonMark\Node\StringContainerInterface;

final class AttributeCallbacks
{
    public static function cssClass(Node $node): string
    {
        return 'from-static-method';
    }

    /**
     * Typed against an interface rather than against `Node` itself
     */
    public static function stringContainerClass(StringContainerInterface $node): string
    {
        return 'from-interface-typed';
    }

    public function __invoke(Node $node): string
    {
        return 'from-invokable';
    }
}
