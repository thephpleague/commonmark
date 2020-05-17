<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Mention\LinkGenerator;

use League\CommonMark\Extension\Mention\LinkGenerator\StringTemplateLinkGenerator;
use League\CommonMark\Inline\Element\Text;
use PHPUnit\Framework\TestCase;

final class StringTemplateLinkGeneratorTest extends TestCase
{
    public function testIt()
    {
        $generator = new StringTemplateLinkGenerator('https://www.twitter.com/%s');

        $link = $generator->generateLink('@', 'colinodell');

        $this->assertSame('https://www.twitter.com/colinodell', $link->getUrl());

        $label = $link->firstChild();
        assert($label instanceof Text);
        $this->assertSame('@colinodell', $label->getContent());
    }
}
