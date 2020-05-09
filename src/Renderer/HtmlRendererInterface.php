<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Renderer;

use League\CommonMark\Node\Block\Document;

/**
 * Renders a parsed Document AST to HTML
 */
interface HtmlRendererInterface
{
    /**
     * Render the given Document node (and all of its children) to HTML
     */
    public function renderDocument(Document $node): string;
}
