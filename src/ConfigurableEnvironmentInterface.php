<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

interface ConfigurableEnvironmentInterface extends EnvironmentInterface
{
    /**
     * @param array $config
     */
    public function mergeConfig(array $config = []);

    /**
     * @param array $config
     */
    public function setConfig(array $config = []);

    /**
     * @param BlockParserInterface $parser
     *
     * @return self
     */
    public function addBlockParser(BlockParserInterface $parser);

    /**
     * @param InlineParserInterface $parser
     *
     * @return self
     */
    public function addInlineParser(InlineParserInterface $parser);

    /**
     * @param InlineProcessorInterface $processor
     *
     * @return self
     */
    public function addInlineProcessor(InlineProcessorInterface $processor);

    /**
     * @param DocumentProcessorInterface $processor
     *
     * @return self
     */
    public function addDocumentProcessor(DocumentProcessorInterface $processor);

    /**
     * @param string                 $blockClass
     * @param BlockRendererInterface $blockRenderer
     *
     * @return self
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer);

    /**
     * @param string                  $inlineClass
     * @param InlineRendererInterface $renderer
     *
     * @return self
     */
    public function addInlineRenderer($inlineClass, InlineRendererInterface $renderer);
}
