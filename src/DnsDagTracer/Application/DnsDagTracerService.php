<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Application;

use App\DnsDagTracer\Domain\Engine\DnsDagEngine;
use App\DnsDagTracer\Domain\Model\DnsDagResult;
use App\DnsDagTracer\Domain\Model\QueryType;

final readonly class DnsDagTracerService
{
    private DnsDagEngine $engine;

    public function __construct(?DnsDagEngine $engine = null)
    {
        $this->engine = $engine ?? new DnsDagEngine();
    }

    public function trace(string $domain, ?string $queryType = null): DnsDagResult
    {
        $type = QueryType::fromString($queryType);

        return $this->engine->trace($domain, $type);
    }
}
