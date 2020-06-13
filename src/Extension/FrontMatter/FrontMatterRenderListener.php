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

namespace League\CommonMark\Extension\FrontMatter;

use League\CommonMark\Event\DocumentRenderedEvent;

final class FrontMatterRenderListener
{
    public function __invoke(DocumentRenderedEvent $event): void
    {
        $frontMatter = $event->getOutput()->getDocument()->getData('front_matter');
        if ($frontMatter === null) {
            return;
        }

        $event->replaceOutput(new RenderedContentWithFrontMatter(
            $event->getOutput()->getDocument(),
            $event->getOutput()->getContent(),
            $frontMatter
        ));
    }
}
