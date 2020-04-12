<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional;

use mikehaertl\shellcommand\Command;
use PHPUnit\Framework\TestCase;

@trigger_error(sprintf('The "%s" class is deprecated since league/commonmark 1.4.', AbstractBinTest::class), E_USER_DEPRECATED);

/**
 * @deprecated
 */
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
