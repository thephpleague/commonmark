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
    public const APPLY_NONE     = '';
    public const APPLY_ALL      = 'all';
    public const APPLY_EXTERNAL = 'external';
    public const APPLY_INTERNAL = 'internal';

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
            if (! $event->isEntering()) {
                continue;
            }

            $link = $event->getNode();
            if (! ($link instanceof Link)) {
                continue;
            }

            $host = \parse_url($link->getUrl(), PHP_URL_HOST);
            if (! \is_string($host)) {
                // Something is terribly wrong with this URL
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
            'nofollow'   => $this->environment->getConfig('external_link/nofollow', self::APPLY_NONE),
            'noopener'   => $this->environment->getConfig('external_link/noopener', self::APPLY_EXTERNAL),
            'noreferrer' => $this->environment->getConfig('external_link/noreferrer', self::APPLY_EXTERNAL),
        ];

        foreach ($options as $type => $option) {
            switch (true) {
                case $option === self::APPLY_ALL:
                case $isExternal && $option === self::APPLY_EXTERNAL:
                case ! $isExternal && $option === self::APPLY_INTERNAL:
                    $link->data->append('attributes/rel', $type);
            }
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
