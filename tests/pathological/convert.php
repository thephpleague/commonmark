#!/usr/bin/env php
<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

ini_set('memory_limit', '1024M');

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

require_once __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', 'stderr');
ini_set('xdebug.max_nesting_level', '999999');

$stdin = fopen('php://stdin', 'r');
if (stream_set_blocking($stdin, true)) {
    $markdown = stream_get_contents($stdin);
}
fclose($stdin);

if (empty($markdown)) {
    fwrite(STDERR, "No input provided\n");
    exit(1);
}

$config = json_decode($argv[1] ?? '[]', true) ?? [];

$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

// Enable additional extensions if requested
switch ($argv[2] ?? null) {
    case 'table':
        $environment->addExtension(new TableExtension());
        break;
    case 'footnotes':
        $environment->addExtension(new FootnoteExtension());
        break;
    case 'gfm':
    default:
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        break;
}

$converter = new MarkdownConverter($environment);

echo $converter->convert($markdown)->getContent();
