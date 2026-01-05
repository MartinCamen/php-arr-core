<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

use MartinCamen\ArrCore\Concerns\ConvertsFileSize;
use MartinCamen\PhpFileSize\Enums\Unit;
use MartinCamen\PhpFileSize\FileSize;

/**
 * Represents disk space information from *arr APIs.
 *
 * This is a shared data structure used by both Radarr and Sonarr.
 */
final readonly class DiskSpace
{
    use ConvertsFileSize;

    public function __construct(
        public string $path,
        public string $label,
        public FileSize $freeSpace,
        public FileSize $totalSpace,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            path: $data['path'] ?? '',
            label: $data['label'] ?? '',
            freeSpace: FileSize::fromBytes($data['freeSpace'] ?? 0),
            totalSpace: FileSize::fromBytes($data['totalSpace'] ?? 0),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'path'        => $this->path,
            'label'       => $this->label,
            'free_space'  => (int) $this->freeSpace->getBytes(),
            'total_space' => (int) $this->totalSpace->getBytes(),
        ];
    }

    public function usedSpace(): FileSize
    {
        return $this->totalSpace
            ->subBytes($this->freeSpace->getBytes())
            ->evaluate();
    }

    public function usedPercentage(): float
    {
        if ($this->totalSpace->isZero()) {
            return 0.0;
        }

        return round(($this->usedSpace()->getBytes() / $this->totalSpace->getBytes()) * 100, 2);
    }

    public function freePercentage(): float
    {
        return 100.0 - $this->usedPercentage();
    }

    /**
     * Get free space in specified unit.
     */
    public function freeSpaceIn(Unit $unit, ?int $precision = 2): float
    {
        return $this->convertToUnit($this->freeSpace, $unit, $precision);
    }

    /**
     * Get total space in specified unit.
     */
    public function totalSpaceIn(Unit $unit, ?int $precision = 2): float
    {
        return $this->convertToUnit($this->totalSpace, $unit, $precision);
    }

    /**
     * Get used space in specified unit.
     */
    public function usedSpaceIn(Unit $unit, ?int $precision = 2): float
    {
        return $this->convertToUnit($this->usedSpace(), $unit, $precision);
    }
}
