<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Exception;

final class InvalidOptionException extends \UnexpectedValueException
{
    /**
     * @param string  $option      Name/path of the option
     * @param mixed   $valueGiven  The invalid option that was provided
     * @param ?string $description Additional text describing the issue (optional)
     */
    public static function forConfigOption(string $option, $valueGiven, ?string $description = null): self
    {
        $message = \sprintf('Invalid config option for "%s": %s', $option, self::getDebugValue($valueGiven));
        if ($description !== null) {
            $message .= \sprintf(' (%s)', $description);
        }

        return new self($message);
    }

    /**
     * @param string $option     Description of the option
     * @param mixed  $valueGiven The invalid option that was provided
     */
    public static function forParameter(string $option, $valueGiven): self
    {
        return new self(\sprintf('Invalid %s: %s', $option, self::getDebugValue($valueGiven)));
    }

    /**
     * @param mixed $value
     */
    private static function getDebugValue($value): string
    {
        if (\is_object($value)) {
            return \get_class($value);
        }

        return \print_r($value, true);
    }
}
