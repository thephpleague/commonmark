<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Reference;

use League\CommonMark\Reference\ReferenceInterface;
use League\CommonMark\Reference\ReferenceParser;
use PHPUnit\Framework\TestCase;

final class ReferenceParserTest extends TestCase
{
    public function testUnclosedTitleIsNotUsed(): void
    {
        $parser = new ReferenceParser();
        $parser->parse('[foo]: /url');
        $parser->parse('"unclosed');

        $references = $this->getReferences($parser);

        $this->assertCount(1, $references);
        $this->assertSame('foo', $references[0]->getLabel());
        $this->assertSame('/url', $references[0]->getDestination());
        $this->assertSame('', $references[0]->getTitle());
        $this->assertSame('"unclosed', $parser->getParagraphContent());
    }

    public function testTitleFollowedByOtherTextIsNotUsed(): void
    {
        $parser = new ReferenceParser();
        $parser->parse('[foo]: /url');
        $parser->parse('"title" ok');

        $references = $this->getReferences($parser);

        $this->assertCount(1, $references);
        $this->assertSame('', $references[0]->getTitle());
        $this->assertSame('"title" ok', $parser->getParagraphContent());
    }

    public function testGettingReferencesKeepsATitleThatIsStillOpen(): void
    {
        $parser = new ReferenceParser();
        $parser->parse('[first]: /one "good"');
        $parser->parse('[second]: /two "multi');

        $this->assertCount(1, $this->getReferences($parser));

        $parser->parse('line"');

        $references = $this->getReferences($parser);

        $this->assertCount(2, $references);
        $this->assertSame('second', $references[1]->getLabel());
        $this->assertSame("multi\nline", $references[1]->getTitle());
        $this->assertSame('', $parser->getParagraphContent());
    }

    /**
     * @return ReferenceInterface[]
     */
    private function getReferences(ReferenceParser $parser): array
    {
        $references = [];
        foreach ($parser->getReferences() as $reference) {
            $references[] = $reference;
        }

        return $references;
    }
}
