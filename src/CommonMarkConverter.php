<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

/**
 * Converts CommonMark-compatible Markdown to HTML.
 */
final class CommonMarkConverter extends MarkdownConverter
{
    /**
     * Create a new commonmark converter instance.
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [], ?EnvironmentInterface $environment = null)
    {
        if ($environment !== null) {
            if ($config !== []) {
                if (! ($environment instanceof Environment)) {
                    throw new \RuntimeException('Unable to configure the environment as only ' . Environment::class . ' can be configured after instantiation');
                }

                $environment->mergeConfig($config);
            }

            parent::__construct($environment);

            return;
        }

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());

        parent::__construct($environment);
    }
}
