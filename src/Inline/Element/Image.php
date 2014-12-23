<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Element;

class Image extends AbstractWebResource
{
    /**
     * @var InlineCollection
     */
    protected $altText;

    /**
     * @param string $url
     * @param InlineCollection|string $label
     * @param string $title
     */
    public function __construct($url, $label = null, $title = '')
    {
        parent::__construct($url);

        if (is_string($label)) {
            $this->altText = new InlineCollection(array(new Text($label)));
        } else {
            $this->altText = $label;
        }

        if (!empty($title)) {
            $this->attributes['title'] = $title;
        }
    }

    /**
     * @return InlineCollection
     */
    public function getAltText()
    {
        return $this->altText;
    }

    /**
     * @param InlineCollection $label
     *
     * @return $this
     */
    public function setAltText($label)
    {
        if (is_string($label)) {
            $this->altText = new InlineCollection(array(new Text($label)));
        } else {
            $this->altText = $label;
        }

        return $this;
    }
}
