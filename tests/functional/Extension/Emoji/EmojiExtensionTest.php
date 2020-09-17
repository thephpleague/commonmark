<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\Emoji;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Emoji\EmojiExtension;
use PHPUnit\Framework\TestCase;

final class EmojiExtensionTest extends TestCase
{
    /** @var Environment */
    private $environment;

    protected function setUp(): void
    {
        $this->environment = Environment::createCommonMarkEnvironment();
        $this->environment->addExtension(new EmojiExtension());
    }

    public function testWithSampleData(): void
    {
        $markdown = '🙍🏿‍♂️ is leaving on a &#x2708;️. Going to 🇦🇺. Might see some :kangaroo:! <3 Remember to 📱 :D';
        $expected = "<p>🙍🏿‍♂️ is leaving on a ✈️. Going to 🇦🇺. Might see some 🦘! ❤️ Remember to 📱 😀</p>\n";

        $converter = new CommonMarkConverter([], $this->environment);
        $result    = $converter->convertToHtml($markdown);

        $this->assertSame($expected, (string) $result);
    }
}
