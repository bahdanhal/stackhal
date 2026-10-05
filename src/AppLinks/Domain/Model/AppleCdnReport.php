<?php

declare(strict_types=1);

namespace App\AppLinks\Domain\Model;

final readonly class AppleCdnReport
{
    public const string STATE_IN_SYNC = 'in_sync';
    public const string STATE_OUT_OF_SYNC = 'out_of_sync';
    public const string STATE_CDN_ONLY = 'cdn_only';
    public const string STATE_FETCH_FAILED = 'fetch_failed';
    public const string STATE_UNCHECKED = 'unchecked';

    /**
     * @param list<AppLinksDiagnostic> $diagnostics
     */
    public function __construct(
        public string $url,
        public string $state,
        public int $status,
        public ?string $comparedWith = null,
        public ?string $source = null,
        public ?string $failureReason = null,
        public ?string $failureDetails = null,
        public ?int $cacheMaxAgeSeconds = null,
        public ?int $cacheAgeSeconds = null,
        public array $diagnostics = [],
    ) {
    }

    /**
     * @return array{
     *     url: string,
     *     state: string,
     *     status: int,
     *     compared_with: ?string,
     *     source: ?string,
     *     failure_reason: ?string,
     *     failure_details: ?string,
     *     cache_max_age_seconds: ?int,
     *     cache_age_seconds: ?int
     * }
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'state' => $this->state,
            'status' => $this->status,
            'compared_with' => $this->comparedWith,
            'source' => $this->source,
            'failure_reason' => $this->failureReason,
            'failure_details' => $this->failureDetails,
            'cache_max_age_seconds' => $this->cacheMaxAgeSeconds,
            'cache_age_seconds' => $this->cacheAgeSeconds,
        ];
    }
}
