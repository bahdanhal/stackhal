<?php

declare(strict_types=1);

namespace App\RegexTranspiler\Domain\Model;

enum RegexEngine: string
{
    case Pcre = 'pcre';
    case GoRe2 = 'go_re2';
    case JavaScript = 'javascript';
    case Python = 'python';
    case Rust = 'rust';

    public static function fromString(string $value): self
    {
        $normalized = strtolower(trim($value));

        return match ($normalized) {
            'pcre', 'pcre2', 'php' => self::Pcre,
            'go', 'golang', 're2', 'go_re2' => self::GoRe2,
            'js', 'javascript', 'ecmascript' => self::JavaScript,
            'py', 'python', 'python3' => self::Python,
            'rs', 'rust' => self::Rust,
            default => throw new \InvalidArgumentException(sprintf('Unsupported regex engine: "%s"', $value)),
        };
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            self::Pcre => 'PCRE / PCRE2',
            self::GoRe2 => 'Go RE2',
            self::JavaScript => 'JavaScript (ECMAScript)',
            self::Python => 'Python re',
            self::Rust => 'Rust regex',
        };
    }

    public function getEcosystem(): string
    {
        return match ($this) {
            self::Pcre => 'PHP, Nginx, Apache, C/C++',
            self::GoRe2 => 'Golang, Kubernetes, Docker, Envoy',
            self::JavaScript => 'V8, Node.js, Web Browsers',
            self::Python => 'Python 3 Standard Library',
            self::Rust => 'Rust crate, Ripgrep',
        };
    }

    public function getTimeComplexity(): string
    {
        return match ($this) {
            self::Pcre, self::JavaScript, self::Python => 'Exponential worst-case (Backtracking / ReDoS prone)',
            self::GoRe2, self::Rust => 'Guaranteed O(N) Linear time (DFA/NFA, Zero ReDoS)',
        };
    }

    public function isLinearTime(): bool
    {
        return match ($this) {
            self::GoRe2, self::Rust => true,
            self::Pcre, self::JavaScript, self::Python => false,
        };
    }

    public function supportsLookaround(): bool
    {
        return match ($this) {
            self::Pcre, self::JavaScript, self::Python => true,
            self::GoRe2, self::Rust => false,
        };
    }

    public function supportsBackreferences(): bool
    {
        return match ($this) {
            self::Pcre, self::JavaScript, self::Python => true,
            self::GoRe2, self::Rust => false,
        };
    }

    public function supportsAtomicGroups(): bool
    {
        return match ($this) {
            self::Pcre => true,
            self::GoRe2, self::JavaScript, self::Python, self::Rust => false,
        };
    }

    public function supportsPossessiveQuantifiers(): bool
    {
        return match ($this) {
            self::Pcre => true,
            self::GoRe2, self::JavaScript, self::Python, self::Rust => false,
        };
    }

    public function supportsRecursion(): bool
    {
        return match ($this) {
            self::Pcre => true,
            self::GoRe2, self::JavaScript, self::Python, self::Rust => false,
        };
    }

    /**
     * @return list<string>
     */
    public function getNamedGroupSyntax(): array
    {
        return match ($this) {
            self::Pcre => ['(?P<name>...)', '(?<name>...)'],
            self::GoRe2, self::Python, self::Rust => ['(?P<name>...)'],
            self::JavaScript => ['(?<name>...)'],
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->value,
            'name' => $this->getDisplayName(),
            'ecosystem' => $this->getEcosystem(),
            'time_complexity' => $this->getTimeComplexity(),
            'is_linear_time' => $this->isLinearTime(),
            'supports_lookaround' => $this->supportsLookaround(),
            'supports_backreferences' => $this->supportsBackreferences(),
            'supports_atomic_groups' => $this->supportsAtomicGroups(),
            'supports_possessive_quantifiers' => $this->supportsPossessiveQuantifiers(),
            'supports_recursion' => $this->supportsRecursion(),
            'named_group_syntax' => $this->getNamedGroupSyntax(),
        ];
    }
}
