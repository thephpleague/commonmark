<?php
namespace CommonMarkExt\Strikethrough;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\Xml;

class StrikethroughRenderer implements InlineRendererInterface
{
    /**
     * @param AbstractInline $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement|string
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Strikethrough)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }
        $attrs = [];
        foreach ($inline->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value, true);
        }

        return new HtmlElement('del', $attrs, Xml::escape($inline->getContent()));
    }
}
