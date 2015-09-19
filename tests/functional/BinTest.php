<?php

namespace League\CommonMark\Tests\Functional;

use mikehaertl\shellcommand\Command;

class BinTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the behavior of not providing any Markdown input
     */
    public function testNoArgsOrStdin()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->execute();

        $this->assertEquals(1, $cmd->getExitCode());
        $this->assertEmpty($cmd->getOutput());
        $this->assertContains('Usage:', $cmd->getError());
    }

    /**
     * Tests the -h flag
     */
    public function testHelpShortFlag()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('-h');
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $this->assertContains('Usage:', $cmd->getOutput());
    }

    /**
     * Tests the --help option
     */
    public function testHelpOption()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('--help');
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $this->assertContains('Usage:', $cmd->getOutput());
    }

    /**
     * Tests the behavior of using unknown options
     */
    public function testUnknownOption()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg('--foo');
        $cmd->execute();

        $this->assertEquals(1, $cmd->getExitCode());
        $this->assertContains('Unknown option', $cmd->getError());
    }

    /**
     * Tests converting a file by filename
     */
    public function testFileArgument()
    {
        $cmd = new Command($this->getPathToCommonmark());
        $cmd->addArg($this->getPathToData('atx_header.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('atx_header.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }

    /**
     * Tests converting Markdown from STDIN
     */
    public function testStdin()
    {
        $cmd = new Command(sprintf('cat %s | %s ', $this->getPathToData('atx_header.md'), $this->getPathToCommonmark()));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('atx_header.html')));
        $this->assertEquals($expectedContents, trim($cmd->getOutput()));
    }

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
        return realpath(__DIR__ . '/data/' . $file);
    }
}
