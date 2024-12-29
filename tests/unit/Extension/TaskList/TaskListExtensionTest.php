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

namespace League\CommonMark\Tests\Unit\Extension\TaskList;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class TaskListExtensionTest extends TestCase
{
    public function testTaskLists(): void
    {
        $input = <<<'EOT'
- [x] foo
  - [ ] bar
  - [X] baz
- [ ] bim

* [x] foo
* [X] bar
* [ ] baz

This works for ordered lists too:

1. [x] foo
2. [X] bar
3. [ ] baz

Some examples which should not match:

 - Checkbox [x] in the middle
 - Checkbox at the end [ ]
 - [  ] too many spaces
 - **[x] Checkbox inside of emphasis**
 - No text, as shown in these examples:
   - [x]
   - [ ]
   -    [x]
   -           [x]

Here's a test using `<del>`:

 - [x] <del>Checkbox inside of strikeout</del>

And another which does not render the checkbox:

 - <del>[x] Checkbox inside of strikeout</del>

EOT;

        $expected = <<<'EOT'
<ul>
<li><input checked="" disabled="" type="checkbox"> foo
<ul>
<li><input disabled="" type="checkbox"> bar</li>
<li><input checked="" disabled="" type="checkbox"> baz</li>
</ul>
</li>
<li><input disabled="" type="checkbox"> bim</li>
</ul>
<ul>
<li><input checked="" disabled="" type="checkbox"> foo</li>
<li><input checked="" disabled="" type="checkbox"> bar</li>
<li><input disabled="" type="checkbox"> baz</li>
</ul>
<p>This works for ordered lists too:</p>
<ol>
<li><input checked="" disabled="" type="checkbox"> foo</li>
<li><input checked="" disabled="" type="checkbox"> bar</li>
<li><input disabled="" type="checkbox"> baz</li>
</ol>
<p>Some examples which should not match:</p>
<ul>
<li>Checkbox [x] in the middle</li>
<li>Checkbox at the end [ ]</li>
<li>[  ] too many spaces</li>
<li><strong>[x] Checkbox inside of emphasis</strong></li>
<li>No text, as shown in these examples:
<ul>
<li>[x]</li>
<li>[ ]</li>
<li>[x]</li>
<li>
<pre><code>      [x]
</code></pre>
</li>
</ul>
</li>
</ul>
<p>Here's a test using <code>&lt;del&gt;</code>:</p>
<ul>
<li><input checked="" disabled="" type="checkbox"> <del>Checkbox inside of strikeout</del></li>
</ul>
<p>And another which does not render the checkbox:</p>
<ul>
<li><del>[x] Checkbox inside of strikeout</del></li>
</ul>

EOT;

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TaskListExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, $converter->convert($input)->getContent());
    }
}
