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

namespace League\CommonMark\Extension\Mention;

use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;

final class MentionExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $mentions = $environment->getConfig('mentions', []);
        foreach ($mentions as $name => $mention) {
            foreach (['symbol', 'regex', 'generator'] as $key) {
                if (! \array_key_exists($key, $mention)) {
                    throw new \RuntimeException(\sprintf('Missing "%s" from MentionParser configuration', $key));
                }
            }

            if ($mention['generator'] instanceof MentionGeneratorInterface) {
                $environment->addInlineParser(new MentionParser($mention['symbol'], $mention['regex'], $mention['generator']));
            } elseif (\is_string($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithStringTemplate($mention['symbol'], $mention['regex'], $mention['generator']));
            } elseif (\is_callable($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithCallback($mention['symbol'], $mention['regex'], $mention['generator']));
            } else {
                throw new \RuntimeException(\sprintf('The "generator" provided for the MentionParser configuration must be a string template, callable, or an object that implements %s.', MentionGeneratorInterface::class));
            }
        }
    }
}
