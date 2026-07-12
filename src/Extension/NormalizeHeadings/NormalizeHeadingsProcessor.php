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

namespace League\CommonMark\Extension\NormalizeHeadings;

use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\NodeIterator;
use League\Config\ConfigurationInterface;

final class NormalizeHeadingsProcessor implements EnvironmentAwareInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    public function setEnvironment(EnvironmentInterface $environment): void
    {
        $this->config = $environment->getConfiguration();
    }

    public function __invoke(DocumentParsedEvent $event): void
    {
        $minLevel = (int) $this->config->get('normalize_headings/min_level');
        $maxLevel = (int) $this->config->get('normalize_headings/max_level');

        foreach ($event->getDocument()->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (! $node instanceof Heading) {
                continue;
            }

            if ($node->getLevel() < $minLevel) {
                $node->setLevel($minLevel);
            } elseif ($node->getLevel() > $maxLevel) {
                $node->setLevel($maxLevel);
            }
        }
    }
}
