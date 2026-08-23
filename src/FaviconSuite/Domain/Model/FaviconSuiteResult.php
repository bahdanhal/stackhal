<?php

declare(strict_types=1);

namespace App\FaviconSuite\Domain\Model;

final readonly class FaviconSuiteResult
{
    /**
     * @param list<FaviconBundleFile> $files
     * @param list<string> $htmlTags
     * @param list<FaviconDiagnostic> $diagnostics
     */
    public function __construct(
        public bool $isValid,
        public array $files,
        public array $htmlTags,
        public array $diagnostics,
        public bool $darkModeInjected,
        public ?string $svgContent = null,
        public ?string $manifestContent = null,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getErrorCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'error') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return list<string>
     */
    public function getWarningCodes(): array
    {
        $codes = [];
        foreach ($this->diagnostics as $diagnostic) {
            if ($diagnostic->severity === 'warning') {
                $codes[] = $diagnostic->code;
            }
        }

        return $codes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'valid' => $this->isValid,
            'dark_mode_injected' => $this->darkModeInjected,
            'html_tags' => $this->htmlTags,
            'files' => array_map(static fn (FaviconBundleFile $f) => $f->toArray(), $this->files),
            'diagnostics' => array_map(static fn (FaviconDiagnostic $d) => $d->toArray(), $this->diagnostics),
            'manifest_content' => $this->manifestContent,
        ];
    }
}
