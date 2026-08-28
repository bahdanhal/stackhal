<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Application\HttpFetcher;
use Bahdan\SafeHttpClient\SafeHttpFetcher as BaseSafeHttpFetcher;

class SafeHttpFetcher extends BaseSafeHttpFetcher implements HttpFetcher
{
}
