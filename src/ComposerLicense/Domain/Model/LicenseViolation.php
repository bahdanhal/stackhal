<?php

declare(strict_types=1);

namespace App\ComposerLicense\Domain\Model;

final readonly class LicenseViolation
{
    public function __construct(
        public string $packageName,
        public string $version,
        public string $license,
        public LicenseClassification $classification,
        public string $dependencyPath,
        public string $description,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'package_name' => $this->packageName,
            'version' => $this->version,
            'license' => $this->license,
            'classification' => $this->classification->value,
            'is_strong_copyleft' => $this->classification->isStrongCopyleft(),
            'dependency_path' => $this->dependencyPath,
            'description' => $this->description,
        ];
    }
}
