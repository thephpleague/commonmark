<?php

namespace League\CommonMark\Tests\functional;

use League\CommonMark\CommonMarkConverter;
use PHPUnit\Framework\TestCase;

class MaximumNestingLevelTest extends TestCase
{
    public function testThatWeCanHitTheLimit()
    {
        $converter = new CommonMarkConverter(['max_nesting_level' => 2]);

        $markdown = '> > Foo';
        $expected = '<blockquote>
<blockquote>
<p>Foo</p>
</blockquote>
</blockquote>
';

        $this->assertEquals($expected, $converter->convertToHtml($markdown));
    }

    public function testThatWeCannotExceedTheLimit()
    {
        $converter = new CommonMarkConverter(['max_nesting_level' => 2]);

        $markdown = '> > > > > > Foo';
        $expected = '<blockquote>
<blockquote>
<p>&gt; &gt; &gt; &gt; Foo</p>
</blockquote>
</blockquote>
';

        $this->assertEquals($expected, $converter->convertToHtml($markdown));
    }
}
