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

namespace League\CommonMark\Tests\Functional\Extension\DescriptionList;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class DescriptionListRegressionTest extends TestCase
{
    /**
     * @see https://github.com/thephpleague/commonmark/issues/692
     */
    public function testIssue692Regression(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DescriptionListExtension());

        $converter = new MarkdownConverter($environment);

        $input = <<<MD
[Lorem ipsum dolor sit amet][foo], consectetur adipiscing elit. Cras vitae
fringilla nulla. Etiam ac lectus scelerisque justo laoreet cursus. Mauris ut
dictum urna. Integer sit amet nibh aliquam, aliquet orci ut, condimentum diam.
Vestibulum varius purus at pulvinar feugiat. Donec facilisis mauris non sapien
ullamcorper semper. Nunc quis sapien eu metus tempor elementum a at mi. Fusce
at nisi et lectus lobortis rutrum.

[foo]: https://example.com
MD;

        $expected = <<<HTML
<p><a href="https://example.com">Lorem ipsum dolor sit amet</a>, consectetur adipiscing elit. Cras vitae
fringilla nulla. Etiam ac lectus scelerisque justo laoreet cursus. Mauris ut
dictum urna. Integer sit amet nibh aliquam, aliquet orci ut, condimentum diam.
Vestibulum varius purus at pulvinar feugiat. Donec facilisis mauris non sapien
ullamcorper semper. Nunc quis sapien eu metus tempor elementum a at mi. Fusce
at nisi et lectus lobortis rutrum.</p>

HTML;

        $this->assertSame($expected, $converter->convert($input)->getContent());
    }
}
