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

namespace League\CommonMark\Extension\ExternalLink;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\Config\ConfigurationInterface;

final class ExternalLinkProcessor
{
    public const APPLY_NONE     = '';
    public const APPLY_ALL      = 'all';
    public const APPLY_EXTERNAL = 'external';
    public const APPLY_INTERNAL = 'internal';

    /** @psalm-readonly */
    private ConfigurationInterface $config;

    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    public function __invoke(DocumentParsedEvent $e): void
    {
        $internalHosts   = $this->config->get('external_link/internal_hosts');
        $openInNewWindow = $this->config->get('external_link/open_in_new_window');
        $classes         = $this->config->get('external_link/html_class');

        foreach ($e->getDocument()->iterator() as $link) {
            if (! ($link instanceof Link)) {
                continue;
            }

            $host = self::getHost($link->getUrl());
            if ($host === null) {
                // Relative links and non-network schemes like mailto: have no host to compare
                continue;
            }

            if (self::hostMatches($host, $internalHosts)) {
                $link->data->set('external', false);
                $this->applyRelAttribute($link, false);
                continue;
            }

            // Host does not match our list
            $this->markLinkAsExternal($link, $openInNewWindow, $classes);
        }
    }

    private function markLinkAsExternal(Link $link, bool $openInNewWindow, string $classes): void
    {
        $link->data->set('external', true);
        $this->applyRelAttribute($link, true);

        if ($openInNewWindow) {
            $link->data->set('attributes/target', '_blank');
        }

        if ($classes !== '') {
            $link->data->append('attributes/class', $classes);
        }
    }

    private function applyRelAttribute(Link $link, bool $isExternal): void
    {
        $options = [
            'nofollow'   => $this->config->get('external_link/nofollow'),
            'noopener'   => $this->config->get('external_link/noopener'),
            'noreferrer' => $this->config->get('external_link/noreferrer'),
        ];

        foreach ($options as $type => $option) {
            switch (true) {
                case $option === self::APPLY_ALL:
                case $isExternal && $option === self::APPLY_EXTERNAL:
                case ! $isExternal && $option === self::APPLY_INTERNAL:
                    $link->data->append('attributes/rel', $type);
            }
        }

        // No rel attributes? Mark the attribute as 'false' so LinkRenderer doesn't add defaults
        if (! $link->data->has('attributes/rel')) {
            $link->data->set('attributes/rel', false);
        }
    }

    /**
     * Determines which host a browser would navigate to, which parse_url() doesn't always agree with
     * on its own (for example, it finds no host in "https:///example.com" or "///example.com")
     */
    private static function getHost(string $url): ?string
    {
        // Browsers ignore these characters, so links not created by the parser could use them to obscure the host
        $url = \str_replace(["\t", "\n", "\r"], '', \trim($url, "\x00..\x20"));

        // For these "special" schemes (and scheme-relative URLs), browsers accept any number of slashes before the
        // host, even none, and treat "\" like "/". parse_url() needs exactly "//", so we rewrite the URL to match.
        // Without slashes ("https:example.com") a browser may instead resolve the URL relative to the current page,
        // but we can't know the page's scheme, so we assume the worst and treat it as a host.
        if (\preg_match('~^(?:(?:https?|ftp|wss?):|(?=[/\\\\]{2}))~i', $url, $matches) === 1) {
            $url = '//' . \ltrim(\strtr(\substr($url, \strlen($matches[0])), '\\', '/'), '/');
        }

        $host = \parse_url($url, PHP_URL_HOST);

        return \is_string($host) && $host !== '' ? $host : null;
    }

    /**
     * @internal This method is only public so we can easily test it. DO NOT USE THIS OUTSIDE OF THIS EXTENSION!
     *
     * @param non-empty-string|list<non-empty-string> $compareTo
     */
    public static function hostMatches(string $host, $compareTo): bool
    {
        foreach ((array) $compareTo as $c) {
            if (\strpos($c, '/') === 0) {
                if (\preg_match($c, $host)) {
                    return true;
                }
            } elseif ($c === $host) {
                return true;
            }
        }

        return false;
    }
}
