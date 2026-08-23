<?php

declare(strict_types=1);

namespace App\AppLinks\Domain\Model;

final readonly class AppLinksDiagnostic
{
    public function __construct(
        public string $code,
        public string $severity,
        public string $title,
        public string $description,
    ) {
    }

    /**
     * @return array{
     *     code: string,
     *     severity: string,
     *     title: string,
     *     description: string
     * }
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'severity' => $this->severity,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
