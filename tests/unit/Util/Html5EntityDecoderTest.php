<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Html5EntityDecoder;
use PHPUnit\Framework\TestCase;

final class Html5EntityDecoderTest extends TestCase
{
    public function testEntityToChar(): void
    {
        $this->assertEquals('©', Html5EntityDecoder::decode('&copy;'));
        $this->assertEquals('&copy', Html5EntityDecoder::decode('&copy'));
        $this->assertEquals('&MadeUpEntity;', Html5EntityDecoder::decode('&MadeUpEntity;'));
        $this->assertEquals('#', Html5EntityDecoder::decode('&#35;'));
        $this->assertEquals('Æ', Html5EntityDecoder::decode('&AElig;'));
        $this->assertEquals('Ď', Html5EntityDecoder::decode('&Dcaron;'));
    }

    /**
     * @dataProvider htmlEntityDataProvider
     */
    public function testAllHtml5EntityReferences(string $entity, string $decoded): void
    {
        $this->assertEquals($decoded, \html_entity_decode($entity, ENT_QUOTES | ENT_HTML5, 'UTF-8'), \sprintf('Failed parsing the "%s" entity', $entity));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function htmlEntityDataProvider(): iterable
    {
        // Test data from https://html.spec.whatwg.org/multipage/entities.json
        $data = \json_decode(\file_get_contents(__DIR__ . '/entities.json'), true);
        foreach ($data as $entity => $info) {
            // Per the spec, we only care about entities that have a trailing semi-colon.
            // See https://spec.commonmark.org/0.29/#entity-references
            if (\substr($entity, -1, 1) === ';') {
                yield [$entity, $info['characters']];
            }
        }
    }
}
