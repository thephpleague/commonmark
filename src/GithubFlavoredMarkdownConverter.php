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
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

/**
 * Converts Github Flavored Markdown to HTML.
 */
class GithubFlavoredMarkdownConverter extends MarkdownConverter
{
    /**
     * Create a new commonmark converter instance.
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [], ?EnvironmentInterface $environment = null)
    {
        // Passing in an $environment is deprecated
        if ($environment !== null) {
            if ($config !== []) {
                if (! ($environment instanceof EnvironmentBuilderInterface)) {
                    throw new \RuntimeException('Unable to configure the environment as only ' . EnvironmentBuilderInterface::class . ' instances can be configured');
                }

                $environment->mergeConfig($config);
            }

            parent::__construct($environment);

            return;
        }

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        parent::__construct($environment);
    }
}
