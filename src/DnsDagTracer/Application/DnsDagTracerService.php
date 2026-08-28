<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Application;

use App\DnsDagTracer\Domain\Engine\DnsDagEngine;
use App\DnsDagTracer\Domain\Model\DnsDagResult;
use App\DnsDagTracer\Domain\Model\QueryType;

final readonly class DnsDagTracerService
{
    public function __construct(private DnsDagEngine $engine)
    {
    }

    public function trace(string $domain, ?string $queryType = null): DnsDagResult
    {
        $type = QueryType::fromString($queryType);

        return $this->engine->trace($domain, $type);
    }
}
