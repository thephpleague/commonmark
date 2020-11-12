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
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

/**
 * Converts Github Flavored Markdown to HTML.
 */
final class GithubFlavoredMarkdownConverter extends MarkdownConverter
{
    /**
     * Create a new commonmark converter instance.
     *
     * @param array<string, mixed>      $config
     * @param EnvironmentInterface|null $environment DEPRECATED - Instantiate a MarkdownConverter instead
     */
    public function __construct(array $config = [], ?EnvironmentInterface $environment = null)
    {
        // Passing in an $environment is deprecated
        if ($environment !== null) {
            @\trigger_error('Passing an $environment into the GithubFlavoredMarkdownConverter constructor is deprecated in league/commonmark v2.0 and will be removed in v3.0; use MarkdownConverter instead of CommonMarkConverter', \E_USER_DEPRECATED);
            if ($config !== []) {
                if (! ($environment instanceof Environment)) {
                    throw new \RuntimeException('Unable to configure the environment as only ' . Environment::class . ' can be configured after instantiation');
                }

                @\trigger_error('Configuring custom environments via the constructor is deprecated in league/commonmark v2.0 and will be removed in v3.0; configure it beforehand and create MarkdownConverter with it instead', \E_USER_DEPRECATED);
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
