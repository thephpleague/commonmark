<?php

require_once __DIR__.'/../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Util\HtmlFilter;

$converter = new CommonMarkConverter([
    'html_input' => HtmlFilter::ESCAPE,
    'allow_unsafe_links' => false,
]);

echo $converter->convertToHtml($_GET['input']);

$converter = new CommonMarkConverter([
    'html_input' => HtmlFilter::STRIP,
    'allow_unsafe_links' => false,
]);

echo $converter->convertToHtml($_GET['input']);
