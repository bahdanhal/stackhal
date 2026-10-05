<?php

declare(strict_types=1);

namespace App\AppLinks\Domain\Engine;

use App\AppLinks\Domain\Model\AppleCdnReport;
use App\AppLinks\Domain\Model\AppLinksDiagnostic;

/**
 * Compares the AASA served by a domain with the copy Apple's associated-domains CDN serves to devices.
 */
final class AppleCdnComparator
{
    private const string CDN_BASE_URL = 'https://app-site-association.cdn-apple.com/a/v1/';
    private const string HOSTNAME_REGEX = '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9-]{2,63}$/';
    private const int MAX_HOSTNAME_LENGTH = 253;
    private const int MAX_HEADER_TEXT_LENGTH = 300;
    private const array AASA_PATHS = ['/.well-known/apple-app-site-association', '/apple-app-site-association'];

    public function cdnUrl(string $domain): ?string
    {
        if (strlen($domain) > self::MAX_HOSTNAME_LENGTH || preg_match(self::HOSTNAME_REGEX, $domain) !== 1) {
            return null;
        }

        return self::CDN_BASE_URL . $domain;
    }

    public function originUrl(string $domain): string
    {
        return 'https://' . $domain . self::AASA_PATHS[0];
    }

    /**
     * Returns the last URL Apple reports fetching from when it is another AASA location on the same domain,
     * such as the www host after a redirect or the legacy root path.
     *
     * @param array<string, list<string>> $cdnHeaders
     */
    public function alternateSource(string $domain, array $cdnHeaders): ?string
    {
        $headers = array_change_key_case($cdnHeaders, CASE_LOWER);
        $candidates = array_map(trim(...), explode(',', implode(',', $headers['apple-from'] ?? [])));

        foreach (array_reverse($candidates) as $candidate) {
            $parts = parse_url($candidate);
            if (!is_array($parts) || array_diff_key($parts, ['scheme' => 1, 'host' => 1, 'path' => 1]) !== []) {
                continue;
            }
            $host = strtolower($parts['host'] ?? '');
            if (
                ($parts['scheme'] ?? '') !== 'https'
                || ($host !== $domain && !str_ends_with($host, '.' . $domain))
                || $this->cdnUrl($host) === null
                || !in_array($parts['path'] ?? '', self::AASA_PATHS, true)
            ) {
                continue;
            }

            $url = 'https://' . $host . $parts['path'];

            return $url === $this->originUrl($domain) ? null : $url;
        }

        return null;
    }

    /**
     * @param array<string, list<string>> $cdnHeaders
     */
    public function compare(
        string $cdnUrl,
        string $originUrl,
        bool $originServed,
        string $originBody,
        int $cdnStatus,
        string $cdnBody,
        array $cdnHeaders,
        ?string $cdnError,
    ): AppleCdnReport {
        $headers = array_change_key_case($cdnHeaders, CASE_LOWER);
        $source = $this->headerText($headers, 'apple-from');
        $reason = $this->headerText($headers, 'apple-failure-reason');
        $details = $this->headerText($headers, 'apple-failure-details');
        $maxAge = preg_match('/max-age=(\d+)/i', $headers['cache-control'][0] ?? '', $match) === 1
            ? (int) $match[1]
            : null;
        $age = ctype_digit($headers['age'][0] ?? '') ? (int) $headers['age'][0] : null;
        $cacheNote = $this->cacheNote($age, $maxAge);

        if ($cdnError !== null || $cdnStatus === 0) {
            $state = AppleCdnReport::STATE_UNCHECKED;
            $diagnostic = $this->unchecked();
        } elseif ($cdnStatus === 200) {
            if (!$originServed) {
                $state = AppleCdnReport::STATE_CDN_ONLY;
                $diagnostic = new AppLinksDiagnostic(
                    code: 'WARN_AASA_CDN_ORIGIN_MISMATCH',
                    severity: 'warning',
                    title: 'Apple CDN Has an AASA the Well-Known URL Did Not Return',
                    description: sprintf(
                        "Apple's CDN serves an association file for this domain%s, but "
                        . '/.well-known/apple-app-site-association did not return one to StackHal. '
                        . 'Devices use the CDN copy until Apple refreshes it.%s',
                        $source !== null ? sprintf(' (fetched from %s)', $source) : '',
                        $cacheNote,
                    ),
                );
            } elseif ($this->canonical($originBody) === $this->canonical($cdnBody)) {
                $state = AppleCdnReport::STATE_IN_SYNC;
                $diagnostic = new AppLinksDiagnostic(
                    code: 'INFO_AASA_CDN_IN_SYNC',
                    severity: 'info',
                    title: 'Apple CDN Copy Matches Your Server',
                    description: sprintf("Apple's CDN serves the same association file as %s.", $originUrl),
                );
            } else {
                $state = AppleCdnReport::STATE_OUT_OF_SYNC;
                $diagnostic = new AppLinksDiagnostic(
                    code: 'WARN_AASA_CDN_OUT_OF_SYNC',
                    severity: 'warning',
                    title: 'Apple CDN Serves a Different AASA',
                    description: sprintf(
                        "Apple's CDN returns a copy that differs from %s. Devices use the CDN copy, "
                        . 'so the version on your server is not live for users yet.%s',
                        $originUrl,
                        $cacheNote,
                    ),
                );
            }
        } elseif ($reason !== null) {
            $state = AppleCdnReport::STATE_FETCH_FAILED;
            $reported = sprintf('"%s"%s', $reason, $details !== null ? sprintf(' (%s)', $details) : '');
            $diagnostic = $originServed
                ? new AppLinksDiagnostic(
                    code: 'ERR_AASA_CDN_FETCH_FAILED',
                    severity: 'error',
                    title: 'Apple CDN Cannot Fetch Your AASA',
                    description: sprintf(
                        "Your server returned the file to StackHal, but Apple's CDN reports %s. "
                        . 'iOS 14 and later read associations through this CDN, so universal links cannot verify '
                        . "until it succeeds. Check for a firewall, bot-protection or geo rule that blocks Apple's "
                        . 'fetcher, a redirect, or a slow response.%s',
                        $reported,
                        $cacheNote,
                    ),
                )
                : new AppLinksDiagnostic(
                    code: 'INFO_AASA_CDN_FAILURE_REPORTED',
                    severity: 'info',
                    title: 'Apple CDN Reports a Fetch Failure',
                    description: sprintf("Apple's CDN reports %s for this domain.%s", $reported, $cacheNote),
                );
        } else {
            $state = AppleCdnReport::STATE_UNCHECKED;
            $diagnostic = $this->unchecked();
        }

        return new AppleCdnReport(
            url: $cdnUrl,
            state: $state,
            status: $cdnStatus,
            comparedWith: $originServed ? $originUrl : null,
            source: $source,
            failureReason: $reason,
            failureDetails: $details,
            cacheMaxAgeSeconds: $maxAge,
            cacheAgeSeconds: $age,
            diagnostics: [$diagnostic],
        );
    }

    private function unchecked(): AppLinksDiagnostic
    {
        return new AppLinksDiagnostic(
            code: 'INFO_AASA_CDN_UNCHECKED',
            severity: 'info',
            title: 'Apple CDN Not Checked',
            description: "StackHal could not read Apple's CDN response for this domain, so the cached copy was not compared.",
        );
    }

    /**
     * @param array<string, list<string>> $headers
     */
    private function headerText(array $headers, string $name): ?string
    {
        $value = trim(implode(', ', $headers[$name] ?? []));
        if ($value === '') {
            return null;
        }

        return mb_substr((string) preg_replace('/[\x00-\x1F\x7F]+/', ' ', $value), 0, self::MAX_HEADER_TEXT_LENGTH);
    }

    private function canonical(string $body): string
    {
        try {
            $decoded = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return trim($body);
        }

        return (string) json_encode($this->sortKeys($decoded), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function sortKeys(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $sorted = array_map($this->sortKeys(...), $value);
        if (!array_is_list($sorted)) {
            ksort($sorted);
        }

        return $sorted;
    }

    private function cacheNote(?int $age, ?int $maxAge): string
    {
        if ($age === null && $maxAge === null) {
            return '';
        }
        if ($age === null) {
            return sprintf(" Apple's CDN response can be cached for up to %s.", $this->duration((int) $maxAge));
        }
        if ($maxAge === null) {
            return sprintf(" Apple's CDN response is %s old.", $this->duration($age));
        }

        return sprintf(
            " Apple's CDN response is %s old and can be cached for up to %s.",
            $this->duration($age),
            $this->duration($maxAge),
        );
    }

    private function duration(int $seconds): string
    {
        [$amount, $unit] = match (true) {
            $seconds < 60 => [$seconds, 'second'],
            $seconds < 3600 => [(int) round($seconds / 60), 'minute'],
            default => [(int) round($seconds / 3600), 'hour'],
        };

        return sprintf('%d %s%s', $amount, $unit, $amount === 1 ? '' : 's');
    }
}
