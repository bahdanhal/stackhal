<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure;

use App\Shared\Infrastructure\Http\NativeHostResolver;
use App\Shared\Infrastructure\Http\UrlGuard;
use Bahdan\SafeHttpClient\DnsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class NativeHostResolverTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    public function testReturnsIpv4BeforeIpv6AndDropsDuplicates(): void
    {
        $resolver = new NativeHostResolver(static fn (string $host, int $type): array => match ($type) {
            DNS_A => [['ip' => '203.0.113.10'], ['ip' => '203.0.113.10'], ['ip' => '203.0.113.11']],
            DNS_AAAA => [['ipv6' => '2001:db8::1']],
            default => [],
        });

        self::assertSame(
            ['example.com' => ['203.0.113.10', '203.0.113.11', '2001:db8::1']],
            $resolver->resolveMany(['example.com', 'example.com']),
        );
    }

    public function testReturnsNoAddressesWhenLookupsFailOrCarryNoAddress(): void
    {
        $failing = new NativeHostResolver(static fn (string $host, int $type): false => false);
        $malformed = new NativeHostResolver(static fn (string $host, int $type): array => [['target' => 'cdn.example']]);

        self::assertSame(['missing.example' => []], $failing->resolveMany(['missing.example']));
        self::assertSame(['odd.example' => []], $malformed->resolveMany(['odd.example']));
    }

    public function testResolvesInsideAFiberItDoesNotOwn(): void
    {
        $resolver = new NativeHostResolver(static fn (string $host, int $type): array => $type === DNS_A
            ? [['ip' => '203.0.113.10']]
            : []);
        $fiber = new \Fiber(static fn (): array => $resolver->resolveMany(['example.com']));

        $fiber->start();

        self::assertTrue($fiber->isTerminated());
        self::assertSame(['example.com' => ['203.0.113.10']], $fiber->getReturn());
    }

    public function testUrlGuardIsWiredWithTheBlockingResolver(): void
    {
        $container = self::getContainer();
        $guard = $container->get(UrlGuard::class);
        $property = new \ReflectionProperty(\Bahdan\SafeHttpClient\UrlGuard::class, 'dnsResolver');

        self::assertInstanceOf(NativeHostResolver::class, $container->get(DnsResolverInterface::class));
        self::assertInstanceOf(NativeHostResolver::class, $property->getValue($guard));
    }
}
