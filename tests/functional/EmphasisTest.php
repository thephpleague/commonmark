<?php

namespace League\CommonMark\Tests\Functional;

use mikehaertl\shellcommand\Command;

class EmphasisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns the full path the commonmark "binary"
     *
     * @return string
     */
    protected function getPathToCommonmark()
    {
        return realpath(__DIR__ . '/../../bin/commonmark');
    }

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
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('emstrong.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with em enabled
     */
    public function testEm()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('--enable-strong=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('em.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with strong enabled
     */
    public function testStrong()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('--enable-em=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('strong.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with em and strong disabled
     */
    public function testNone()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('--enable-strong=', 0);
        $cmd->addArg('--enable-em=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('disabled.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with underscores disabled
     */
    public function testAsterisks()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('--use-underscore=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('asterisks.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Test emphasis parsing with asterisks disabled
     */
    public function testUnderscores()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('--use-asterisk=', 0);
        $cmd->addArg($this->getPathToData('input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('underscores.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }
}
