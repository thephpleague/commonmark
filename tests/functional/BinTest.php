<?php

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;
use mikehaertl\shellcommand\Command;

class BinTest extends AbstractBinTest
{
    /**
     * Tests the behavior of not providing any Markdown input
     */
    public function testNoArgsOrStdin()
    {
        $cmd = $this->createCommand();
        $cmd->execute();

        $this->assertEquals(1, $cmd->getExitCode());
        $this->assertEmpty($cmd->getOutput());

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $this->assertContains('Usage:', $cmd->getError());
        }
    }

    /**
     * Tests the -h flag
     */
    public function testHelpShortFlag()
    {
        $cmd = $this->createCommand();
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
        $cmd = $this->createCommand();
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
        $cmd = $this->createCommand();
        $cmd->addArg('--foo');
        $cmd->execute();

        $this->assertEquals(1, $cmd->getExitCode());

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $this->assertContains('Unknown option', $cmd->getError());
        }
    }

    /**
     * Tests converting a file by filename
     */
    public function testFileArgument()
    {
        $cmd = $this->createCommand();
        $cmd->addArg($this->getPathToData('atx_heading.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('atx_heading.html')));
        $this->assertEquals($expectedContents, $cmd->getOutput());
    }

    /**
     * Tests converting Markdown from STDIN
     */
    public function testStdin()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Test skipped: STDIN is not supported on Windows');
        }

        $cmd = new Command(sprintf('cat %s | %s ', $this->getPathToData('atx_heading.md'), $this->getPathToCommonmark()));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('atx_heading.html')));
        $this->assertEquals($expectedContents, $cmd->getOutput());
    }

    /**
     * Tests converting Markdown without the --safe flag
     */
    public function testUnsafe()
    {
        $cmd = $this->createCommand();
        $cmd->addArg($this->getPathToData('safe/input.md'));
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('safe/unsafe_output.html')));
        $this->assertEquals($expectedContents, $cmd->getOutput());
    }

    /**
     * Tests converting Markdown with the --safe flag
     */
    public function testSafe()
    {
        $cmd = $this->createCommand();
        $cmd->addArg($this->getPathToData('safe/input.md'));
        $cmd->addArg('--safe');
        $cmd->execute();

        $this->assertEquals(0, $cmd->getExitCode());
        $expectedContents = trim(file_get_contents($this->getPathToData('safe/safe_output.html')));
        $this->assertEquals($expectedContents, $cmd->getOutput());
    }

    /**
     * Tests that the version flags show the current version
     */
    public function testVersion()
    {
        foreach (['-v', '--version'] as $arg) {
            $cmd = $this->createCommand();
            $cmd->addArg($arg);
            $cmd->execute();

            $this->assertEquals(0, $cmd->getExitCode());
            $this->assertEquals(CommonMarkConverter::VERSION, trim($cmd->getOutput()));
        }
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
