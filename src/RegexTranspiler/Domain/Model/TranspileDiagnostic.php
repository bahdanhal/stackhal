<?php

declare(strict_types=1);

namespace App\RegexTranspiler\Domain\Model;

final readonly class TranspileDiagnostic
{
    public const SEVERITY_ERROR = 'error';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_INFO = 'info';

    public function __construct(
        public string $severity,
        public string $code,
        public string $title,
        public string $description,
    ) {
    }

    public static function unsupportedLookaround(): self
    {
        return new self(
            severity: self::SEVERITY_ERROR,
            code: 'ERR_UNSUPPORTED_LOOKAROUND',
            title: 'Lookaround Assertions Forbidden',
            // phpcs:ignore Generic.Files.LineLength
            description: 'Lookahead (?=...), (?!...) and Lookbehind (?<=...), (?<!...) are not supported in RE2 / Rust linear engines to guarantee O(N) time and prevent ReDoS attacks.'
        );
    }

    public static function unsupportedBackreference(): self
    {
        return new self(
            severity: self::SEVERITY_ERROR,
            code: 'ERR_UNSUPPORTED_BACKREFERENCE',
            title: 'Backreferences Forbidden',
            description: 'Backreferences (\\1, \\k<name>) require non-deterministic backtracking and are forbidden in DFA/RE2 engines.'
        );
    }

    public static function unsupportedRecursion(): self
    {
        return new self(
            severity: self::SEVERITY_ERROR,
            code: 'ERR_UNSUPPORTED_RECURSION',
            title: 'Pattern Recursion Unsupported',
            description: 'Recursive subroutine calls (?R), (?1) are exclusive to PCRE and cannot be compiled in RE2, JS, or standard Python.'
        );
    }

    public static function atomicGroupConverted(): self
    {
        return new self(
            severity: self::SEVERITY_ERROR,
            code: 'ERR_ATOMIC_GROUP_NOT_EQUIVALENT',
            title: 'Atomic Group Has No Safe Equivalent',
            description: 'Removing atomic behavior can change which strings match. The source pattern is preserved for manual rewriting.'
        );
    }

    public static function possessiveQuantifierConverted(): self
    {
        return new self(
            severity: self::SEVERITY_ERROR,
            code: 'ERR_POSSESSIVE_QUANTIFIER_NOT_EQUIVALENT',
            title: 'Possessive Quantifier Has No Safe Equivalent',
            description: 'Replacing a possessive quantifier with a greedy one can change matching behavior. The source pattern is preserved.'
        );
    }

    public static function invalidPattern(string $reason): self
    {
        return new self(
            severity: self::SEVERITY_ERROR,
            code: 'ERR_INVALID_PATTERN',
            title: 'Invalid Regular Expression Syntax',
            description: $reason
        );
    }

    public static function inlineModifiersUnsupported(): self
    {
        return new self(
            severity: self::SEVERITY_ERROR,
            code: 'ERR_INLINE_MODIFIERS_REQUIRE_EXTERNAL_FLAGS',
            title: 'JavaScript Requires External Flags',
            description: 'JavaScript does not support this PCRE inline modifier group. Move supported modifiers to RegExp flags manually.'
        );
    }

    public static function namedGroupSyntaxTranspiled(): self
    {
        return new self(
            severity: self::SEVERITY_WARNING,
            code: 'WARN_NAMED_GROUP_SYNTAX_TRANSPILLED',
            title: 'Named Group Syntax Transpiled',
            description: 'Named capture group syntax was adjusted to match the target engine convention.'
        );
    }

    public static function linearTimeGuaranteed(): self
    {
        return new self(
            severity: self::SEVERITY_INFO,
            code: 'INFO_LINEAR_TIME_GUARANTEED',
            title: 'Linear Execution Guaranteed',
            description: 'Target engine operates with guaranteed linear O(N) execution time with zero ReDoS risk.'
        );
    }

    /**
     * @return array{severity: string, code: string, title: string, description: string}
     */
    public function toArray(): array
    {
        return [
            'severity' => $this->severity,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
