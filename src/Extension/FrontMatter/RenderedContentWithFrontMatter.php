<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\FrontMatter;

use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContent;

/**
 * @psalm-immutable
 */
final class RenderedContentWithFrontMatter extends RenderedContent
{
    /**
     * @var mixed
     *
     * @psalm-readonly
     */
    private $frontMatter;

    /**
     * @param mixed $frontMatter
     */
    public function __construct(Document $document, string $html, $frontMatter)
    {
        parent::__construct($document, $html);

        $this->frontMatter = $frontMatter;
    }

    /**
     * @return mixed
     */
    public function getFrontMatter()
    {
        return $this->frontMatter;
    }
}
