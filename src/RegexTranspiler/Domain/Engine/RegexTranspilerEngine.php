<?php

declare(strict_types=1);

namespace App\RegexTranspiler\Domain\Engine;

use App\RegexTranspiler\Domain\Model\EngineCompatibility;
use App\RegexTranspiler\Domain\Model\RegexEngine;
use App\RegexTranspiler\Domain\Model\RegexTranspileResult;
use App\RegexTranspiler\Domain\Model\TranspileDiagnostic;

final class RegexTranspilerEngine
{
    /**
     * Transpile a pattern from source engine to target engine and compute full compatibility matrix.
     */
    public function transpile(
        string $pattern,
        RegexEngine $sourceEngine = RegexEngine::Pcre,
        RegexEngine $targetEngine = RegexEngine::GoRe2,
    ): RegexTranspileResult {
        $trimmedPattern = trim($pattern);

        $compatibilityMatrix = [];
        $engines = [
            RegexEngine::Pcre,
            RegexEngine::GoRe2,
            RegexEngine::JavaScript,
            RegexEngine::Python,
            RegexEngine::Rust,
        ];

        foreach ($engines as $engine) {
            $compatibilityMatrix[] = $this->evaluateEngineCompatibility($trimmedPattern, $sourceEngine, $engine);
        }

        $targetCompatibility = $this->evaluateEngineCompatibility($trimmedPattern, $sourceEngine, $targetEngine);

        return new RegexTranspileResult(
            sourceEngine: $sourceEngine,
            targetEngine: $targetEngine,
            sourcePattern: $trimmedPattern,
            transpiledPattern: $targetCompatibility->transpiledPattern,
            isCompatible: $targetCompatibility->isCompatible,
            diagnostics: $targetCompatibility->diagnostics,
            compatibilityMatrix: $compatibilityMatrix,
        );
    }

    /**
     * Evaluate single target engine compatibility and perform dialect transpilation.
     */
    public function evaluateEngineCompatibility(
        string $pattern,
        RegexEngine $sourceEngine,
        RegexEngine $targetEngine,
    ): EngineCompatibility {
        if ($pattern === '') {
            return new EngineCompatibility(
                engine: $targetEngine,
                isCompatible: true,
                isLinearTime: $targetEngine->isLinearTime(),
                transpiledPattern: '',
                diagnostics: $targetEngine->isLinearTime() ? [TranspileDiagnostic::linearTimeGuaranteed()] : [],
            );
        }

        $diagnostics = [];
        $errorsFound = false;

        $hasLookaround = false;
        $hasBackreference = false;
        $hasRecursion = false;
        $hasAtomicGroup = false;
        $hasPossessiveQuantifier = false;
        $hasNamedGroupTranspiled = false;

        $length = strlen($pattern);
        $i = 0;
        $inCharClass = false;
        $output = '';

        while ($i < $length) {
            $char = $pattern[$i];

            // 1. Backslash escape handling
            if ($char === '\\') {
                $output .= $this->processEscape(
                    $pattern,
                    $i,
                    $inCharClass,
                    $targetEngine,
                    $hasBackreference,
                    $errorsFound
                );
                continue;
            }

            // 2. Character class opening
            if ($char === '[' && !$inCharClass) {
                $inCharClass = true;
                $output .= '[';
                $i++;
                if ($i < $length && $pattern[$i] === '^') {
                    $output .= '^';
                    $i++;
                }
                if ($i < $length && $pattern[$i] === ']') {
                    $output .= ']';
                    $i++;
                }
                continue;
            }

            // 3. Character class closing
            if ($char === ']' && $inCharClass) {
                $inCharClass = false;
                $output .= ']';
                $i++;

                $transpiledQuantifier = $this->transpilePossessiveQuantifier($pattern, $i, $targetEngine);
                if ($transpiledQuantifier !== null) {
                    $output .= $transpiledQuantifier['output'];
                    $i = $transpiledQuantifier['new_index'];
                    if ($transpiledQuantifier['converted']) {
                        $hasPossessiveQuantifier = true;
                    }
                }
                continue;
            }

            // 4. Inside character class: literals
            if ($inCharClass) {
                $output .= $char;
                $i++;
                continue;
            }

            // 5. Group opening
            if ($char === '(') {
                $output .= $this->processGroupOpening(
                    $pattern,
                    $i,
                    $targetEngine,
                    $hasLookaround,
                    $hasRecursion,
                    $hasAtomicGroup,
                    $hasNamedGroupTranspiled,
                    $errorsFound
                );
                continue;
            }

            // 6. Group closing
            if ($char === ')') {
                $output .= ')';
                $i++;

                $transpiledQuantifier = $this->transpilePossessiveQuantifier($pattern, $i, $targetEngine);
                if ($transpiledQuantifier !== null) {
                    $output .= $transpiledQuantifier['output'];
                    $i = $transpiledQuantifier['new_index'];
                    if ($transpiledQuantifier['converted']) {
                        $hasPossessiveQuantifier = true;
                    }
                }
                continue;
            }

            // 7. Standalone possessive quantifier
            $transpiledQuantifier = $this->transpilePossessiveQuantifier($pattern, $i, $targetEngine);
            if ($transpiledQuantifier !== null && $transpiledQuantifier['converted']) {
                $output .= $transpiledQuantifier['output'];
                $i = $transpiledQuantifier['new_index'];
                $hasPossessiveQuantifier = true;
                continue;
            }

            // 8. Default literal character
            $output .= $char;
            $i++;
        }

        if ($hasLookaround && !$targetEngine->supportsLookaround()) {
            $diagnostics[] = TranspileDiagnostic::unsupportedLookaround();
        }
        if ($hasBackreference && !$targetEngine->supportsBackreferences()) {
            $diagnostics[] = TranspileDiagnostic::unsupportedBackreference();
        }
        if ($hasRecursion && !$targetEngine->supportsRecursion()) {
            $diagnostics[] = TranspileDiagnostic::unsupportedRecursion();
        }
        if ($hasAtomicGroup && !$targetEngine->supportsAtomicGroups()) {
            $diagnostics[] = TranspileDiagnostic::atomicGroupConverted();
        }
        if ($hasPossessiveQuantifier && !$targetEngine->supportsPossessiveQuantifiers()) {
            $diagnostics[] = TranspileDiagnostic::possessiveQuantifierConverted();
        }
        if ($hasNamedGroupTranspiled) {
            $diagnostics[] = TranspileDiagnostic::namedGroupSyntaxTranspiled();
        }

        $isCompatible = !$errorsFound;

        if ($isCompatible && $targetEngine->isLinearTime()) {
            $diagnostics[] = TranspileDiagnostic::linearTimeGuaranteed();
        }

        return new EngineCompatibility(
            engine: $targetEngine,
            isCompatible: $isCompatible,
            isLinearTime: $targetEngine->isLinearTime(),
            transpiledPattern: $output,
            diagnostics: $diagnostics,
        );
    }

    private function processEscape(
        string $pattern,
        int &$i,
        bool $inCharClass,
        RegexEngine $targetEngine,
        bool &$hasBackreference,
        bool &$errorsFound,
    ): string {
        $length = strlen($pattern);
        if ($i + 1 >= $length) {
            $i++;

            return '\\';
        }

        $nextChar = $pattern[$i + 1];

        if ($inCharClass) {
            $i += 2;

            return '\\' . $nextChar;
        }

        // Backreferences: \1 .. \9
        if (ctype_digit($nextChar) && $nextChar !== '0') {
            $hasBackreference = true;
            if (!$targetEngine->supportsBackreferences()) {
                $errorsFound = true;
            }
            $i += 2;

            return '\\' . $nextChar;
        }

        // Named backreference \k<name> or \k'name'
        if ($nextChar === 'k' && $i + 2 < $length) {
            $delimiter = $pattern[$i + 2];
            if ($delimiter === '<' || $delimiter === "'") {
                $hasBackreference = true;
                if (!$targetEngine->supportsBackreferences()) {
                    $errorsFound = true;
                }
            }
        }

        $i += 2;

        return '\\' . $nextChar;
    }

    private function processGroupOpening(
        string $pattern,
        int &$i,
        RegexEngine $targetEngine,
        bool &$hasLookaround,
        bool &$hasRecursion,
        bool &$hasAtomicGroup,
        bool &$hasNamedGroupTranspiled,
        bool &$errorsFound,
    ): string {
        $length = strlen($pattern);

        if ($i + 1 >= $length || $pattern[$i + 1] !== '?') {
            $i++;

            return '(';
        }

        // Lookarounds: (?=, (?!, (?<=, (?<!
        if (
            $this->startsWith($pattern, $i, '(?=') ||
            $this->startsWith($pattern, $i, '(?!') ||
            $this->startsWith($pattern, $i, '(?<=') ||
            $this->startsWith($pattern, $i, '(?<!')
        ) {
            $hasLookaround = true;
            if (!$targetEngine->supportsLookaround()) {
                $errorsFound = true;
            }
            $i++;

            return '(';
        }

        // Recursion: (?R), (?0), (?1), (?&name)
        if (
            $this->startsWith($pattern, $i, '(?R)') ||
            $this->startsWith($pattern, $i, '(?0)') ||
            preg_match('/^\(\?(?:R|\d+|&[a-zA-Z0-9_]+)\)/', substr($pattern, $i)) === 1
        ) {
            $hasRecursion = true;
            if (!$targetEngine->supportsRecursion()) {
                $errorsFound = true;
            }
            $i++;

            return '(';
        }

        // Atomic group: (? >...)
        if ($this->startsWith($pattern, $i, '(?>')) {
            $hasAtomicGroup = true;
            $i += 3;

            if (!$targetEngine->supportsAtomicGroups()) {
                return '(?:';
            }

            return '(?>';
        }

        // Named group: (?P<name>...)
        if ($this->startsWith($pattern, $i, '(?P<')) {
            $endAngle = strpos($pattern, '>', $i + 4);
            if ($endAngle !== false) {
                $groupName = substr($pattern, $i + 4, $endAngle - ($i + 4));
                $i = $endAngle + 1;

                if ($targetEngine === RegexEngine::JavaScript) {
                    $hasNamedGroupTranspiled = true;

                    return '(?<' . $groupName . '>';
                }

                return '(?P<' . $groupName . '>';
            }
        }

        // Named group: (?<name>...)
        if ($this->startsWith($pattern, $i, '(?<')) {
            $endAngle = strpos($pattern, '>', $i + 3);
            if ($endAngle !== false) {
                $groupName = substr($pattern, $i + 3, $endAngle - ($i + 3));
                $i = $endAngle + 1;

                if (
                    $targetEngine === RegexEngine::GoRe2 ||
                    $targetEngine === RegexEngine::Python ||
                    $targetEngine === RegexEngine::Rust
                ) {
                    $hasNamedGroupTranspiled = true;

                    return '(?P<' . $groupName . '>';
                }

                return '(?<' . $groupName . '>';
            }
        }

        $i++;

        return '(';
    }

    /**
     * Check if a possessive quantifier exists at $index in $pattern, and transpile if necessary.
     *
     * @return array{output: string, new_index: int, converted: bool}|null
     */
    private function transpilePossessiveQuantifier(string $pattern, int $index, RegexEngine $targetEngine): ?array
    {
        $length = strlen($pattern);
        if ($index >= $length) {
            return null;
        }

        $char = $pattern[$index];

        // ++, *+, ?+
        if (($char === '+' || $char === '*' || $char === '?') && $index + 1 < $length && $pattern[$index + 1] === '+') {
            if (!$targetEngine->supportsPossessiveQuantifiers()) {
                return [
                    'output' => $char,
                    'new_index' => $index + 2,
                    'converted' => true,
                ];
            }

            return [
                'output' => $char . '+',
                'new_index' => $index + 2,
                'converted' => false,
            ];
        }

        // {m,n}+ or {m,}+ or {n}+
        if ($char === '{') {
            $closeBrace = strpos($pattern, '}', $index + 1);
            if ($closeBrace !== false && $closeBrace + 1 < $length && $pattern[$closeBrace + 1] === '+') {
                $quantifierBody = substr($pattern, $index, $closeBrace - $index + 1);
                if (!$targetEngine->supportsPossessiveQuantifiers()) {
                    return [
                        'output' => $quantifierBody,
                        'new_index' => $closeBrace + 2,
                        'converted' => true,
                    ];
                }

                return [
                    'output' => $quantifierBody . '+',
                    'new_index' => $closeBrace + 2,
                    'converted' => false,
                ];
            }
        }

        return null;
    }

    private function startsWith(string $haystack, int $offset, string $needle): bool
    {
        return substr($haystack, $offset, strlen($needle)) === $needle;
    }
}
