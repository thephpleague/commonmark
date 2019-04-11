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
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

/**
 * Interface for an Environment which can be configured with config settings, parsers, processors, and renderers
 */
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
     * Registers the given extension with the Environment
     *
     * @param ExtensionInterface $extension
     *
     * @return ConfigurableEnvironmentInterface
     */
    public function addExtension(ExtensionInterface $extension): ConfigurableEnvironmentInterface;

    /**
     * Registers the given block parser with the Environment
     *
     * @param BlockParserInterface $parser   Block parser instance
     * @param int                  $priority Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addBlockParser(BlockParserInterface $parser, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given inline parser with the Environment
     *
     * @param InlineParserInterface $parser   Inline parser instance
     * @param int                   $priority Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addInlineParser(InlineParserInterface $parser, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given inline processor with the Environment
     *
     * @param InlineProcessorInterface $processor Inline processor instance
     * @param int                      $priority  Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addInlineProcessor(InlineProcessorInterface $processor, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given document processor with the Environment
     *
     * @param DocumentProcessorInterface $processor Document processor instance
     * @param int                        $priority  Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addDocumentProcessor(DocumentProcessorInterface $processor, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * @param string                 $blockClass    The fully-qualified block element class name the renderer below should handle
     * @param BlockRendererInterface $blockRenderer The renderer responsible for rendering the type of element given above
     * @param int                    $priority      Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given inline renderer with the Environment
     *
     * @param string                  $inlineClass The fully-qualified inline element class name the renderer below should handle
     * @param InlineRendererInterface $renderer    The renderer responsible for rendering the type of element given above
     * @param int                     $priority    Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addInlineRenderer(string $inlineClass, InlineRendererInterface $renderer, int $priority = 0): ConfigurableEnvironmentInterface;
}
