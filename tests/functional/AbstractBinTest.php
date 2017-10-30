<?php

namespace League\CommonMark\Tests\Functional;

use mikehaertl\shellcommand\Command;
use PHPUnit\Framework\TestCase;

abstract class AbstractBinTest extends TestCase
{
    /**
     * @return string
     */
    protected function getPathToCommonmark()
    {
        return realpath(__DIR__ . '/../../bin/commonmark');
    }

    /**
     * @return Command
     */
    protected function createCommand()
    {
        $path = $this->getPathToCommonmark();

        $command = new Command();
        if ($command->getIsWindows()) {
            $command->setCommand('php');
            $command->addArg($path);
        } else {
            $command->setCommand($path);
        }

        return $command;
    }
}
