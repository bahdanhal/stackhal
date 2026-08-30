<?php

declare(strict_types=1);

namespace App\ComposerLicense\Domain\Model;

enum LicenseClassification: string
{
    case PERMISSIVE = 'PERMISSIVE';
    case DUAL_PERMISSIVE_OPTION = 'DUAL_PERMISSIVE_OPTION';
    case WEAK_COPYLEFT = 'WEAK_COPYLEFT';
    case STRONG_COPYLEFT = 'STRONG_COPYLEFT';
    case PROPRIETARY = 'PROPRIETARY';
    case UNKNOWN = 'UNKNOWN';

    public function isCopyleft(): bool
    {
        return $this === self::STRONG_COPYLEFT || $this === self::WEAK_COPYLEFT;
    }

    public function isStrongCopyleft(): bool
    {
        return $this === self::STRONG_COPYLEFT;
    }
}
