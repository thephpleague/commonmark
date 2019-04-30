<?php

require_once __DIR__.'/../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;

$converter = new CommonMarkConverter([
    'html_input' => Environment::HTML_INPUT_ESCAPE,
    'allow_unsafe_links' => false,
]);

echo $converter->convertToHtml($_GET['input']);

$converter = new CommonMarkConverter([
    'html_input' => Environment::HTML_INPUT_STRIP,
    'allow_unsafe_links' => false,
]);

echo $converter->convertToHtml($_GET['input']);
