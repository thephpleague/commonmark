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

class Link extends AbstractWebResource
{
    /**
     * @var InlineCollection
     */
    protected $label;

    /**
     * @param string $url
     * @param InlineCollection|string|null $label
     * @param string $title
     */
    public function __construct($url, $label = null, $title = '')
    {
        parent::__construct($url);

        if ($label === null) {
            $label = $url;
        }

        if (is_string($label)) {
            $this->label = new InlineCollection(array(new Text($label)));
        } else {
            $this->label = $label;
        }

        if (!empty($title)) {
            $this->data['title'] = $title;
        }
    }

    /**
     * @return InlineCollection
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param InlineCollection $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}
