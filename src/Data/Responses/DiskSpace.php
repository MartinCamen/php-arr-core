<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

use MartinCamen\PhpFileSize\FileSize;

/**
 * Represents disk space information from *arr APIs.
 *
 * This is a shared data structure used by both Radarr and Sonarr.
 */
final readonly class DiskSpace
{
    public function __construct(
        public string $path,
        public string $label,
        public int $freeSpace,
        public int $totalSpace,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            path: $data['path'] ?? '',
            label: $data['label'] ?? '',
            freeSpace: $data['freeSpace'] ?? 0,
            totalSpace: $data['totalSpace'] ?? 0,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'path'        => $this->path,
            'label'       => $this->label,
            'free_space'  => $this->freeSpace,
            'total_space' => $this->totalSpace,
        ];
    }

    public function usedSpace(): int
    {
        return $this->totalSpace - $this->freeSpace;
    }

    public function usedPercentage(): float
    {
        if ($this->totalSpace === 0) {
            return 0.0;
        }

        return round(($this->usedSpace() / $this->totalSpace) * 100, 2);
    }

    public function freePercentage(): float
    {
        return 100.0 - $this->usedPercentage();
    }

    public function freeSpaceGb(): float
    {
        return (new FileSize($this->freeSpace))->precision(2)->toGigabytes();
    }

    public function totalSpaceGb(): float
    {
        return (new FileSize($this->totalSpace))->precision(2)->toGigabytes();
    }

    public function usedSpaceGb(): float
    {
        return (new FileSize($this->usedSpace()))->precision(2)->toGigabytes();
    }
}
