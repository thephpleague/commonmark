<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Environment;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Renderer\Block\BlockRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class FakeInjectableBlockRenderer implements BlockRendererInterface, ConfigurationAwareInterface, EnvironmentAwareInterface
{
    use FakeInjectableTrait;

    public function render(AbstractBlock $block, NodeRendererInterface $htmlRenderer, bool $inTightList = false)
    {
    }
}
