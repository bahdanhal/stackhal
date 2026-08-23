<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Model;

final readonly class DnsLayer
{
    /**
     * @param list<DnsServerNode> $nodes
     */
    public function __construct(
        public int $layer,
        public string $name,
        public string $label,
        public string $servers,
        public array $nodes = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'layer' => $this->layer,
            'name' => $this->name,
            'label' => $this->label,
            'servers' => $this->servers,
            'nodes' => array_map(static fn (DnsServerNode $node) => $node->toArray(), $this->nodes),
        ];
    }
}
