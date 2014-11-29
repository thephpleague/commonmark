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

namespace ColinODell\CommonMark;

use ColinODell\CommonMark\Element\BlockElement;
use ColinODell\CommonMark\Element\InlineElement;
use ColinODell\CommonMark\Element\InlineElementInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Renders a parsed AST to HTML
 */
class HtmlRenderer
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'blockSeparator' => "\n",
            'innerSeparator' => "\n",
            'softBreak' => "\n"
        ));
    }

    /**
     * @param string $string
     * @param bool   $preserveEntities
     *
     * @return string
     */
    protected function escape($string, $preserveEntities = false)
    {
        if ($preserveEntities) {
            $string = preg_replace('/[&](?![#](x[a-f0-9]{1,8}|[0-9]{1,8});|[a-z][a-z0-9]{1,31};)/i', '&amp;', $string);
        } else {
            $string = str_replace('&', '&amp;', $string);
        }

        $string = strtr($string, array(
            '<' => '&lt;',
            '>' => '&gt;',
            '"' => '&quot;'
        ));

        return $string;
    }

    /**
     * Helper function to produce content in a pair of HTML tags.
     *
     * @param string      $tag
     * @param array       $attribs
     * @param string|null $contents
     * @param bool        $selfClosing
     *
     * @return string
     */
    protected function inTags($tag, $attribs = array(), $contents = null, $selfClosing = false)
    {
        $result = '<' . $tag;

        foreach ($attribs as $key => $value) {
            $result .= ' ' . $key . '="' . $value . '"';

        }

        if ($contents) {
            $result .= '>' . $contents . '</' . $tag . '>';
        } elseif ($selfClosing) {
            $result .= ' />';
        } else {
            $result .= '></' . $tag . '>';
        }

        return $result;
    }

    /**
     * @param InlineElementInterface $inline
     *
     * @return mixed|string
     *
     * @throws \InvalidArgumentException
     */
    protected function renderInline(InlineElementInterface $inline)
    {
        $attrs = array();
        switch ($inline->getType()) {
            case InlineElement::TYPE_TEXT:
                return $this->escape($inline->getContents());
            case InlineElement::TYPE_SOFTBREAK:
                return $this->options['softBreak'];
            case InlineElement::TYPE_HARDBREAK:
                return $this->inTags('br', array(), '', true) . "\n";
            case InlineElement::TYPE_EMPH:
                return $this->inTags('em', array(), $this->renderInlines($inline->getContents()));
            case InlineElement::TYPE_STRONG:
                return $this->inTags('strong', array(), $this->renderInlines($inline->getContents()));
            case InlineElement::TYPE_HTML:
                return $inline->getContents();
            case InlineElement::TYPE_LINK:
                $attrs['href'] = $this->escape($inline->getAttribute('destination'), true);
                if ($title = $inline->getAttribute('title')) {
                    $attrs['title'] = $this->escape($title, true);
                }

                return $this->inTags('a', $attrs, $this->renderInlines($inline->getAttribute('label')));
            case InlineElement::TYPE_IMAGE:
                $attrs['src'] = $this->escape($inline->getAttribute('destination'), true);
                $alt = $this->renderInlines($inline->getAttribute('label'));
                $alt = preg_replace('/\<[^>]*alt="([^"]*)"[^>]*\>/', '$1', $alt);
                $attrs['alt'] = preg_replace('/\<[^>]*\>/', '', $alt);
                if ($title = $inline->getAttribute('title')) {
                    $attrs['title'] = $this->escape($title, true);
                }

                return $this->inTags('img', $attrs, '', true);
            case InlineElement::TYPE_CODE:
                return $this->inTags('code', array(), $this->escape($inline->getContents()));
            default:
                throw new \InvalidArgumentException('Unknown inline type: ' . $inline->getType());
        }
    }

    /**
     * @param InlineElementInterface[] $inlines
     *
     * @return string
     */
    protected function renderInlines($inlines)
    {
        $result = array();
        foreach ($inlines as $inline) {
            $result[] = $this->renderInline($inline);
        }

        return implode('', $result);
    }

    /**
     * @param BlockElement $block
     * @param bool         $inTightList
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function renderBlock(BlockElement $block, $inTightList = false)
    {
        switch ($block->getType()) {
            case BlockElement::TYPE_DOCUMENT:
                $wholeDoc = $this->renderBlocks($block->getChildren());

                return $wholeDoc === '' ? '' : $wholeDoc . "\n";
            case BlockElement::TYPE_PARAGRAPH:
                if ($inTightList) {
                    return $this->renderInlines($block->getInlineContent());
                } else {
                    return $this->inTags('p', array(), $this->renderInlines($block->getInlineContent()));
                }
            case BlockElement::TYPE_BLOCK_QUOTE:
                $filling = $this->renderBlocks($block->getChildren());
                if ($filling === '') {
                    return $this->inTags('blockquote', array(), $this->options['innerSeparator']);
                } else {
                    return $this->inTags(
                        'blockquote',
                        array(),
                        $this->options['innerSeparator'] . $filling . $this->options['innerSeparator']
                    );
                }
            case BlockElement::TYPE_LIST_ITEM:
                return trim($this->inTags('li', array(), $this->renderBlocks($block->getChildren(), $inTightList)));
            case BlockElement::TYPE_LIST:
                $listData = $block->getExtra('list_data');
                $start = isset($listData['start']) ? $listData['start'] : null;

                $tag = $listData['type'] == BlockElement::LIST_TYPE_UNORDERED ? 'ul' : 'ol';
                $attr = (!$start || $start == 1) ?
                    array() : array('start' => (string)$start);

                return $this->inTags(
                    $tag,
                    $attr,
                    $this->options['innerSeparator'] . $this->renderBlocks(
                        $block->getChildren(),
                        $block->getExtra('tight')
                    ) . $this->options['innerSeparator']
                );
            case BlockElement::TYPE_HEADER:
                $tag = 'h' . $block->getExtra('level');

                return $this->inTags($tag, array(), $this->renderInlines($block->getInlineContent()));

            case BlockElement::TYPE_INDENTED_CODE:
                return $this->inTags(
                    'pre',
                    array(),
                    $this->inTags('code', array(), $this->escape($block->getStringContent()))
                );

            case BlockElement::TYPE_FENCED_CODE:
                $infoWords = preg_split('/ +/', $block->getExtra('info'));
                $attr = count($infoWords) === 0 || strlen(
                    $infoWords[0]
                ) === 0 ? array() : array('class' => 'language-' . $this->escape($infoWords[0], true));
                return $this->inTags(
                    'pre',
                    array(),
                    $this->inTags('code', $attr, $this->escape($block->getStringContent()))
                );

            case BlockElement::TYPE_HTML_BLOCK:
                return $block->getStringContent();

            case BlockElement::TYPE_REFERENCE_DEF:
                return '';

            case BlockElement::TYPE_HORIZONTAL_RULE:
                return $this->inTags('hr', array(), '', true);

            default:
                throw new \RuntimeException('Unknown block type: ' . $block->getType());
        }
    }

    /**
     * @param BlockElement[] $blocks
     * @param bool           $inTightList
     *
     * @return string
     */
    protected function renderBlocks($blocks, $inTightList = false)
    {
        $result = array();
        foreach ($blocks as $block) {
            if ($block->getType() !== 'ReferenceDef') {
                $result[] = $this->renderBlock($block, $inTightList);
            }
        }

        return implode($this->options['blockSeparator'], $result);
    }

    /**
     * @param BlockElement $block
     * @param bool         $inTightList
     *
     * @return string
     *
     * @api
     */
    public function render(BlockElement $block, $inTightList = false)
    {
        return $this->renderBlock($block, $inTightList);
    }
}
