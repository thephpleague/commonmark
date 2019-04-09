---
layout: default
title: Document Processing
---

Document Processing
===================

The best way to manipulate the [Abstract Syntax Tree](/0.19/customization/abstract-syntax-tree/) is by implementing a custom Document Processor.  These are executed once all other processing is done and the document is ready to be rendered. Simply create a class which implements the `DocumentProcessorInterface` which contains a single method:

~~~php
<?php

/**
 * @param Document $document
 *
 * @return void
 */
public function processDocument(Document $document);
~~~

This method receives the root `Document` node which you could then walk across, modifying nodes as needed.

Here's an example of a Document Processor which adds an `external-link` class to external URLs.
 
~~~php
<?php

class ExternalLinkProcessor implements DocumentProcessorInterface, ConfigurationAwareInterface
{
    private $config;

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->config = $configuration;
    }

    /**
     * @param Document $document
     *
     * @return void
     */
    public function processDocument(Document $document)
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            // Only stop at Link nodes when we first encounter them
            if (!($node instanceof Link) || !$event->isEntering()) {
                continue;
            }

            $url = $node->getUrl();
            if ($this->isUrlExternal($url)) {
                $node->data['attributes']['class'] = 'external-link';
            }
        }
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function isUrlExternal($url)
    {
        // Only look at http and https URLs
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host != $this->config->getConfig('host');
    }
}
~~~

And here's how you'd use it:

~~~php
<?php

$env = Environment::createCommonMarkEnvironment();
$env->addDocumentProcessor(new ExternalLinkProcessor());

$converter = new CommonMarkConverter(['host' => 'commonmark.thephpleague.com'], $env);

$input = 'My two favorite sites are <https://google.com> and <https://commonmark.thephpleague.com>';

echo $converter->convertToHtml($input);
~~~

Output (formatted for readability):

~~~html
<p>
    My two favorite sites are
    <a class="external-link" href="https://google.com">https://google.com</a>
    and
    <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a>
</p>
~~~
