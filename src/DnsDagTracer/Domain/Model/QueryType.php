<?php

declare(strict_types=1);

namespace App\DnsDagTracer\Domain\Model;

enum QueryType: string
{
    case A = 'A';
    case AAAA = 'AAAA';
    case CNAME = 'CNAME';
    case TXT = 'TXT';
    case MX = 'MX';
    case NS = 'NS';
    case SOA = 'SOA';
    case CAA = 'CAA';
    case DS = 'DS';
    case DNSKEY = 'DNSKEY';

    public static function fromString(?string $value): self
    {
        if ($value === null || $value === '') {
            return self::A;
        }

        return self::tryFrom(strtoupper(trim($value))) ?? self::A;
    }
}
