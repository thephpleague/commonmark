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

use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\Config\ConfigurationAwareInterface;

final class FakeInjectableRenderer implements NodeRendererInterface, ConfigurationAwareInterface, EnvironmentAwareInterface
{
    use FakeInjectableTrait;

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        return '';
    }
}
