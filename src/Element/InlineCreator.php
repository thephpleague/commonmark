<?php

/*
 * This file is part of the commonmark-php package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ColinODell\CommonMark\Element;

use ColinODell\CommonMark\Util\ArrayCollection;

/**
 * Provides static methods to simplify and standardize the creation of inline elements
 */
class InlineCreator
{
    /**
     * @param string $code
     *
     * @return InlineElement
     */
    public static function createCode($code)
    {
        return new InlineElement(InlineElement::TYPE_CODE, array('c' => $code));
    }

    /**
     * @param string $contents
     *
     * @return InlineElement
     */
    public static function createEmph($contents)
    {
        return new InlineElement(InlineElement::TYPE_EMPH, array('c' => $contents));
    }

    /**
     * @param string $contents
     *
     * @return InlineElement
     */
    public static function createEntity($contents)
    {
        return new InlineElement(InlineElement::TYPE_ENTITY, array('c' => $contents));
    }

    /**
     * @return InlineElement
     */
    public static function createHardbreak()
    {
        return new InlineElement(InlineElement::TYPE_HARDBREAK);
    }

    /**
     * @param string $html
     *
     * @return InlineElement
     */
    public static function createHtml($html)
    {
        return new InlineElement(InlineElement::TYPE_HTML, array('c' => $html));
    }

    /**
     * @param string                      $destination
     * @param string|ArrayCollection|null $label
     * @param string|null                 $title
     *
     * @return InlineElement
     */
    public static function createLink($destination, $label = null, $title = null)
    {
        $attr = array('destination' => $destination);

        if (is_string($label)) {
            $attr['label'] = array(self::createString($label));
        } elseif (is_object($label) && $label instanceof ArrayCollection) {
            $attr['label'] = $label->toArray();
        } elseif (empty($label)) {
            $attr['label'] = array(self::createString($destination));
        } else {
            $attr['label'] = $label;
        }

        if ($title) {
            $attr['title'] = $title;
        }

        return new InlineElement(InlineElement::TYPE_LINK, $attr);
    }

    /**
     * @return InlineElement
     */
    public static function createSoftbreak()
    {
        return new InlineElement(InlineElement::TYPE_SOFTBREAK);
    }

    /**
     * @param string $contents
     *
     * @return InlineElement
     */
    public static function createString($contents)
    {
        return new InlineElement(InlineElement::TYPE_STRING, array('c' => $contents));
    }

    /**
     * @param string $contents
     *
     * @return InlineElement
     */
    public static function createStrong($contents)
    {
        return new InlineElement(InlineElement::TYPE_STRONG, array('c' => $contents));
    }
}
