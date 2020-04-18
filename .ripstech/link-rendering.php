<?php

require_once __DIR__.'/../vendor/autoload.php';

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\LinkRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

$renderer = new LinkRenderer();
$renderer->setConfiguration(new Configuration([
    'allow_unsafe_links' => false,
]));

$inline = new Link($_GET['url'], $_GET['label'], $_GET['title']);
$fakeRenderer = new FakeHtmlRenderer();

echo $renderer->render($inline, $fakeRenderer);
