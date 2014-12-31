<?php
namespace League\CommonMark;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

interface EnvironmentInterface {
    /**
     * @return BlockParserInterface[]
     */
    public function getBlockParsers();

    /**
     * @return InlineParserInterface[]
     */
    public function getInlineParsers();

    /**
     * @return InlineProcessorInterface[]
     */
    public function getInlineProcessors();

    /**
     * @return BlockRendererInterface[]
     */
    public function getBlockRenderers();

    /**
     * @return InlineRendererInterface[]
     */
    public function getInlineRenderers();
}
