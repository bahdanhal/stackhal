<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Application\UrlGuard as UrlGuardPort;
use Bahdan\SafeHttpClient\UrlGuard as BaseUrlGuard;

readonly class UrlGuard extends BaseUrlGuard implements UrlGuardPort
{
}
