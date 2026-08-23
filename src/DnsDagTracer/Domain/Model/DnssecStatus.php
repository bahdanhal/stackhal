<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Model;

enum DnssecStatus: string
{
    case SECURE = 'secure';
    case BOGUS = 'bogus';
    case UNSIGNED = 'unsigned';
    case INDETERMINATE = 'indeterminate';
}
