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

namespace League\CommonMark\Block\Element;

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

class HtmlBlock extends AbstractBlock
{
    const TYPE_1_CODE_CONTAINER = 1;
    const TYPE_2_COMMENT = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    const TYPE_5_CDATA = 5;
    const TYPE_6_BLOCK_ELEMENT = 6;
    const TYPE_7_MISC_ELEMENT = 7;

    /**
     * @var int
     */
    protected $type;

    /**
     * @param int $type
     */
    public function __construct($type)
    {
        parent::__construct();

        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param AbstractBlock $block
     *
     * @return bool
     */
    public function canContain(AbstractBlock $block)
    {
        return false;
    }

    /**
     * Returns true if block type can accept lines of text
     *
     * @return bool
     */
    public function acceptsLines()
    {
        return true;
    }

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    public function isCode()
    {
        return true;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        if ($cursor->isBlank() && ($this->type === self::TYPE_6_BLOCK_ELEMENT || $this->type === self::TYPE_7_MISC_ELEMENT)) {
            return false;
        }

        return true;
    }

    public function finalize(ContextInterface $context)
    {
        parent::finalize($context);

        $this->finalStringContents = implode("\n", $this->getStrings());
    }

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
        $context->getTip()->addLine($cursor->getRemainder());

        // Check for end condition
        if ($this->type >= self::TYPE_1_CODE_CONTAINER && $this->type <= self::TYPE_5_CDATA) {
            if ($cursor->match(RegexHelper::getHtmlBlockCloseRegex($this->type)) !== null) {
                $this->finalize($context);
            }
        }
    }
}
