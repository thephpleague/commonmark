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

namespace League\CommonMark\Tests\PHPStan;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PhpParser\Node;

/**
 * Custom phpstan rule that:
 *
 * 1. Disallows the use of certain mbstring functions that could be problematic
 * 2. Requires an explicit encoding be provided to all `mb_*()` functions that support it
 */
final class MbstringFunctionCallRule implements Rule
{
    private array $disallowedFunctionsThatAlterGlobalSettings = [
        'mb_internal_encoding',
        'mb_regex_encoding',
        'mb_detect_order',
        'mb_language',
    ];

    private array $encodingParamPositionCache = [];

    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof Node\Expr\FuncCall) {
            return [];
        }

        if (! $node->name instanceof Node\Name) {
            return [];
        }

        $functionName = $node->name->toString();
        if (! str_starts_with($functionName, 'mb_')) {
            return [];
        }

        if (\in_array($functionName, $this->disallowedFunctionsThatAlterGlobalSettings, true)) {
            return [\sprintf('Use of %s() is not allowed in this library because it alters global settings', $functionName)];
        }

        $encodingParamPosition = $this->getEncodingParamPosition($functionName);
        if ($encodingParamPosition === null) {
            return [];
        }

        $arg = $node->args[$encodingParamPosition] ?? null;
        if ($arg === null) {
            return [\sprintf('%s() is missing the $encoding param (should be "UTF-8")', $functionName)];
        }

        if (! $arg instanceof Node\Arg) {
            return [];
        }

        $encodingArg = $arg->value;
        if (! ($encodingArg instanceof Node\Scalar\String_)) {
            return [\sprintf('%s() must define the $encoding as "UTF-8"', $functionName)];
        }

        if (! \in_array($encodingArg->value, ['UTF-8', 'ASCII'], true)) {
            return [\sprintf('%s() must define the $encoding as "UTF-8" or "ASCII", not "%s"', $functionName, $encodingArg->value)];
        }

        return [];
    }

    private function getEncodingParamPosition(string $function): ?int
    {
        if (isset($this->encodingParamPositionCache[$function])) {
            return $this->encodingParamPositionCache[$function];
        }

        $reflection = new \ReflectionFunction($function);
        $params     = $reflection->getParameters();

        $encodingParamPosition = null;
        foreach ($params as $i => $param) {
            if ($param->getName() === 'encoding') {
                $encodingParamPosition = $i;
                break;
            }
        }

        $this->encodingParamPositionCache[$function] = $encodingParamPosition;

        return $encodingParamPosition;
    }
}
