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
     * @param int                  $priority
     *
     * @return self
     */
    public function addBlockParser(BlockParserInterface $parser, $priority = 0);

    /**
     * @param InlineParserInterface $parser
     * @param int                   $priority
     *
     * @return self
     */
    public function addInlineParser(InlineParserInterface $parser, $priority = 0);

    /**
     * @param InlineProcessorInterface $processor
     * @param int                      $priority
     *
     * @return self
     */
    public function addInlineProcessor(InlineProcessorInterface $processor, $priority = 0);

    /**
     * @param DocumentProcessorInterface $processor
     * @param int                        $priority
     *
     * @return self
     */
    public function addDocumentProcessor(DocumentProcessorInterface $processor, $priority = 0);

    /**
     * @param string                 $blockClass
     * @param BlockRendererInterface $blockRenderer
     * @param int                    $priority
     *
     * @return self
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer, $priority = 0);

    /**
     * @param string                  $inlineClass
     * @param InlineRendererInterface $renderer
     * @param int                     $priority
     *
     * @return self
     */
    public function addInlineRenderer($inlineClass, InlineRendererInterface $renderer, $priority = 0);
}
