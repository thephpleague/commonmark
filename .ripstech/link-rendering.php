<?php

require_once __DIR__.'/../vendor/autoload.php';

use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;

$renderer = new LinkRenderer();
$renderer->setConfiguration(new Configuration([
    'allow_unsafe_links' => false,
]));

$inline = new Link($_GET['url'], $_GET['label'], $_GET['title']);
$fakeRenderer = new FakeHtmlRenderer();

echo $renderer->render($inline, $fakeRenderer);
