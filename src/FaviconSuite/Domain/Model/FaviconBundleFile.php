<?php

declare(strict_types=1);

namespace App\FaviconSuite\Domain\Model;

final readonly class FaviconBundleFile
{
    /**
     * @param list<array{width: int, height: int}>|null $dimensions
     */
    public function __construct(
        public string $filename,
        public string $mimeType,
        public string $description,
        public ?int $width = null,
        public ?int $height = null,
        public ?array $dimensions = null,
        public ?string $purpose = null,
        public ?float $safeZoneInsetRatio = null,
        public ?string $content = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'filename' => $this->filename,
            'mime_type' => $this->mimeType,
            'description' => $this->description,
        ];

        if ($this->width !== null) {
            $data['width'] = $this->width;
        }

        if ($this->height !== null) {
            $data['height'] = $this->height;
        }

        if ($this->dimensions !== null) {
            $data['dimensions'] = $this->dimensions;
        }

        if ($this->purpose !== null) {
            $data['purpose'] = $this->purpose;
        }

        if ($this->safeZoneInsetRatio !== null) {
            $data['safe_zone_inset_ratio'] = $this->safeZoneInsetRatio;
        }

        if ($this->content !== null) {
            $data['content'] = $this->content;
        }

        return $data;
    }
}
