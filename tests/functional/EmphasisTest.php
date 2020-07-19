<?php

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

namespace League\CommonMark\Tests\Functional;

@trigger_error(sprintf('The "%s" class is deprecated since league/commonmark 1.4.', EmphasisTest::class), E_USER_DEPRECATED);

/**
 * @deprecated
 */
class EmphasisTest extends AbstractBinTest
{
    /**
     * Returns the full path to the test data file
     *
     * @param string $file
     *
     * @return string
     */
    protected function getPathToData($file)
    {
        return realpath(__DIR__ . '/data/emphasis/' . $file);
    }

    /**
     * Test emphasis parsing with em and strong enabled
     */
    public function testEmStrong()
    {
        $cmd = $this->createCommand();
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('emstrong.html')));
        $this->assertStringContainsString($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with em enabled
     */
    public function testEm()
    {
        $cmd = $this->createCommand();
        $cmd->addArg('--enable-strong=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('em.html')));
        $this->assertStringContainsString($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with strong enabled
     */
    public function testStrong()
    {
        $cmd = $this->createCommand();
        $cmd->addArg('--enable-em=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('strong.html')));
        $this->assertStringContainsString($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with em and strong disabled
     */
    public function testNone()
    {
        $cmd = $this->createCommand();
        $cmd->addArg('--enable-strong=', 0);
        $cmd->addArg('--enable-em=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('disabled.html')));
        $this->assertStringContainsString($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with underscores disabled
     */
    public function testAsterisks()
    {
        $cmd = $this->createCommand();
        $cmd->addArg('--use-underscore=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('asterisks.html')));
        $this->assertStringContainsString($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with asterisks disabled
     */
    public function testUnderscores()
    {
        $cmd = $this->createCommand();
        $cmd->addArg('--use-asterisk=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('underscores.html')));
        $this->assertStringContainsString($expectedContents, trim($cmd->getOutput()));
    }
}
