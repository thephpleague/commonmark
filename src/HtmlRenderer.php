<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\ReferenceDefinition;
use League\CommonMark\Inline\Element\AbstractBaseInline;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Renders a parsed AST to HTML
 */
class HtmlRenderer
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(Environment $environment, array $options = array())
    {
        $this->environment = $environment;

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
     * @param string $option
     *
     * @return mixed
     */
    public function getOption($option)
    {
        return $this->options[$option];
    }

    /**
     * @param string $string
     * @param bool   $preserveEntities
     *
     * @return string
     */
    public function escape($string, $preserveEntities = false)
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
    public function inTags($tag, $attribs = array(), $contents = null, $selfClosing = false)
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
     * @param AbstractBaseInline $inline
     *
     * @return mixed|string
     *
     * @throws \RuntimeException
     */
    protected function renderInline(AbstractBaseInline $inline)
    {
        $renderer = $this->environment->getInlineRendererForClass(get_class($inline));
        if (!$renderer) {
            throw new \RuntimeException('Unable to find corresponding renderer for block type ' . get_class($inline));
        }

        return $renderer->render($inline, $this);
    }

    /**
     * @param AbstractBaseInline[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines)
    {
        $result = array();
        foreach ($inlines as $inline) {
            $result[] = $this->renderInline($inline);
        }

        return implode('', $result);
    }

    /**
     * @param AbstractBlock $block
     * @param bool         $inTightList
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function renderBlock(AbstractBlock $block, $inTightList = false)
    {
        $renderer = $this->environment->getBlockRendererForClass(get_class($block));
        if (!$renderer) {
            throw new \RuntimeException('Unable to find corresponding renderer for block type ' . get_class($block));
        }

        return $renderer->render($block, $this, $inTightList);
    }

    /**
     * @param AbstractBlock[] $blocks
     * @param bool            $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false)
    {
        $result = array();
        foreach ($blocks as $block) {
            if (!($block instanceof ReferenceDefinition)) {
                $result[] = $this->renderBlock($block, $inTightList);
            }
        }

        return implode($this->options['blockSeparator'], $result);
    }
}
