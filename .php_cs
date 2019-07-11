<?php

$header = <<<EOF
This is part of the league/commonmark-ext-table package.

(c) Martin Hasoň <martin.hason@gmail.com>
(c) Webuni s.r.o. <info@webuni.cz>
(c) Colin O'Dell <colinodell@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        '@PHP71Migration:risky' => true,
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],
        'header_comment' => [
            'header' => $header,
        ],
        'ordered_imports' => true,
    ))
    ->setLineEnding("\n")
    ->setUsingCache(false)
    ->setFinder(PhpCsFixer\Finder::create()->in([__DIR__.'/src', __DIR__.'/tests']))
;
