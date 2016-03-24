<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Element;

class Link extends AbstractWebResource
{
    /**
     * @param string      $url
     * @param string|null $label
     * @param string      $title
     */
    public function __construct($url, $label = null, $title = '')
    {
        parent::__construct($url);

        if (is_string($label)) {
            $this->appendChild(new Text($label));
        }

        if (!empty($title)) {
            $this->data['title'] = $title;
        }
    }
}
