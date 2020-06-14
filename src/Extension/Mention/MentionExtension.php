<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Mention;

use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class MentionExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $registeredMentions = $environment->getConfig('registered_mentions', []);

        // Retrieve mentions, excluding already registered mentions.
        $mentions = array_diff_key($environment->getConfig('mentions', []), $registeredMentions);

        // Immediately return if there are no mentions to register.
        if (empty($mentions)) {
            return;
        }

        foreach ($mentions as $name => $mention) {
            // Ensure the integrity of the mention configuration.
            foreach (['symbol', 'regex', 'generator'] as $key) {
                if (empty($mention[$key])) {
                    throw new \RuntimeException("Missing \"$key\" from MentionParser configuration");
                }
            }

            // Ensure only one symbol is registered.
            if (in_array($mention['symbol'], array_column($registeredMentions, 'symbol'), true)) {
                throw new \RuntimeException('Only one type of mention symbol (i.e. @ or #) can be registered per the environment configuration');
            }

            if (is_string($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithStringTemplate($mention['symbol'], $mention['regex'], $mention['generator']));
            } elseif (is_callable($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithCallback($mention['symbol'], $mention['regex'], $mention['generator']));
            } else {
                throw new \RuntimeException('The "generator" provided for the MentionParser configuration must be a string template or a callable');
            }

            // Indicate that this mention has been registered.
            $registeredMentions[$name] = $mention;
        }

        // Save the registered mentions.
        $environment->mergeConfig(['registered_mentions' => $registeredMentions]);
    }

    /**
     * @param \League\CommonMark\ConfigurableEnvironmentInterface $environment
     */
    public static function registerGitHubHandle(ConfigurableEnvironmentInterface $environment): void
    {
        $environment->mergeConfig([
            'mentions' => [
                'github_handle' => [
                    'symbol'    => '@',
                    'regex'     => '/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)/',
                    'generator' => 'https://github.com/%s',
                ],
            ],
        ]);
    }

    /**
     * @param \League\CommonMark\ConfigurableEnvironmentInterface $environment
     * @param string                                              $project
     */
    public static function registerGitHubIssue(ConfigurableEnvironmentInterface $environment, string $project): void
    {
        $environment->mergeConfig([
            'mentions' => [
                'github_issue' => [
                    'symbol'    => '#',
                    'regex'     => '/^\d+/',
                    'generator' => "https://github.com/$project/issues/%d",
                ],
            ],
        ]);
    }

    /**
     * @param \League\CommonMark\ConfigurableEnvironmentInterface $environment
     */
    public static function registerTwitterHandle(ConfigurableEnvironmentInterface $environment): void
    {
        $environment->mergeConfig([
            'mentions' => [
                'twitter_handle' => [
                    'symbol'    => '@',
                    'regex'     => '/^[A-Za-z0-9_]{1,15}(?!\w)/',
                    'generator' => 'https://twitter.com/%s',
                ],
            ],
        ]);
    }
}
