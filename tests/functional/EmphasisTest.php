<?php

namespace League\CommonMark\Tests\Functional;

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
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
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
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
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
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
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
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
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
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
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
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }
}
