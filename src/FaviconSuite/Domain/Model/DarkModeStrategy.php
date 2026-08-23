<?php

declare(strict_types=1);

namespace App\FaviconSuite\Domain\Model;

enum DarkModeStrategy: string
{
    case CSS_INVERT_FILL = 'css_invert_fill';
    case CSS_CLASS_SWAP = 'css_class_swap';
    case PRESERVE_COLORS = 'preserve_colors';

    public static function fromString(?string $value): self
    {
        if ($value === null || $value === '') {
            return self::CSS_INVERT_FILL;
        }

        return self::tryFrom(strtolower(trim($value))) ?? self::CSS_INVERT_FILL;
    }
}
