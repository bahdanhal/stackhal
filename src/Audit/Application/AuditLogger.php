<?php

declare(strict_types=1);

namespace App\Audit\Application;

interface AuditLogger
{
    public function newAuditId(): string;

    /** @param array<string, mixed> $context */
    public function log(string $event, array $context = []): void;

    public function safeUrl(?string $url): ?string;

    public function safeError(?string $message): ?string;
}
