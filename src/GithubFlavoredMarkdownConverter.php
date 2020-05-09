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

namespace League\CommonMark;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;

/**
 * Converts Github Flavored Markdown to HTML.
 */
class GithubFlavoredMarkdownConverter extends CommonMarkConverter
{
    /**
     * Create a new commonmark converter instance.
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [], ?EnvironmentInterface $environment = null)
    {
        if ($environment === null) {
            $environment = Environment::createGFMEnvironment();
        }

        parent::__construct($config, $environment);
    }
}
