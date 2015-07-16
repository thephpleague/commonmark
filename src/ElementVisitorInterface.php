<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Inline\Element\AbstractInline;

/**
 * ElementVisitorInterface is the interface the all element visitor classes must implement.
 */
interface ElementVisitorInterface
{
    /**
     * Called before child blocks are visited.
     *
     * @param AbstractBlock $block
     * @param Environment $environment
     *
     * @return AbstractBlock
     */
    public function enterBlock(AbstractBlock $block, Environment $environment);

    /**
     * Called after child blocks are visited.
     *
     * @param AbstractBlock $block
     * @param Environment $environment
     *
     * @return AbstractBlock|false
     */
    public function leaveBlock(AbstractBlock $block, Environment $environment);

    /**
     * Called before child inlines are visited.
     *
     * @param AbstractInline $inline
     * @param Environment $environment
     *
     * @return AbstractInline
     */
    public function enterInline(AbstractInline $inline, Environment $environment);

    /**
     * Called after child inlines are visited.
     *
     * @param AbstractInline $inline
     * @param Environment $environment
     *
     * @return AbstractInline|false
     */
    public function leaveInline(AbstractInline $inline, Environment $environment);

    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return int
     */
    public function getPriority();
}
