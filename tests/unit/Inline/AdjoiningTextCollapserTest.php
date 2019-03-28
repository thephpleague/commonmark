<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Inline;

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Inline\AdjoiningTextCollapser;
use League\CommonMark\Inline\Element\Newline;
use League\CommonMark\Inline\Element\Text;
use PHPUnit\Framework\TestCase;

class AdjoiningTextCollapserTest extends TestCase
{
    public function testCollapseTextNodes()
    {
        $paragraph = new Paragraph();

        $paragraph->appendChild(new Text('https://eventum.example.net/history.php?iss'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('id=107092'));
        $paragraph->appendChild(new Newline(Newline::SOFTBREAK));
        $paragraph->appendChild(new Text('https://gitlab.example.net/group/project/merge'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('requests/39#note'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('150630'));

        AdjoiningTextCollapser::collapseTextNodes($paragraph);

        $children = $paragraph->children();

        $this->assertCount(3, $children);

        $this->assertTrue($children[0] instanceof Text);
        $this->assertEquals('https://eventum.example.net/history.php?iss_id=107092', $children[0]->getContent());

        $this->assertTrue($children[1] instanceof Newline);

        $this->assertTrue($children[2] instanceof Text);
        $this->assertEquals('https://gitlab.example.net/group/project/merge_requests/39#note_150630', $children[2]->getContent());
    }
}
