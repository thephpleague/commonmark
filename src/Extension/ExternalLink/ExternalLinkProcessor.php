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

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;

final class ExternalLinkProcessor
{
    /**
     * @var EnvironmentInterface
     *
     * @psalm-readonly
     */
    private $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function __invoke(DocumentParsedEvent $e): void
    {
        $internalHosts   = $this->environment->getConfig('external_link/internal_hosts', []);
        $openInNewWindow = $this->environment->getConfig('external_link/open_in_new_window', false);
        $classes         = $this->environment->getConfig('external_link/html_class', '');

        $walker = $e->getDocument()->walker();
        while ($event = $walker->next()) {
            if (! $event->isEntering() || ! ($event->getNode() instanceof Link)) {
                continue;
            }

            $link = $event->getNode();
            \assert($link instanceof Link);

            $host = \parse_url($link->getUrl(), PHP_URL_HOST);
            if (empty($host)) {
                // Something is terribly wrong with this URL
                continue;
            }

            if (self::hostMatches($host, $internalHosts)) {
                $link->data['external'] = false;
                continue;
            }

            // Host does not match our list
            $this->markLinkAsExternal($link, $openInNewWindow, $classes);
        }
    }

    private function markLinkAsExternal(Link $link, bool $openInNewWindow, string $classes): void
    {
        $link->data['external']          = true;
        $link->data['attributes']        = $link->getData('attributes', []);
        $link->data['attributes']['rel'] = 'noopener noreferrer';

        if ($openInNewWindow) {
            $link->data['attributes']['target'] = '_blank';
        }

        if (! empty($classes)) {
            $link->data['attributes']['class'] = \trim(($link->data['attributes']['class'] ?? '') . ' ' . $classes);
        }
    }

    /**
     * @internal This method is only public so we can easily test it. DO NOT USE THIS OUTSIDE OF THIS EXTENSION!
     *
     * @param mixed $compareTo
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
