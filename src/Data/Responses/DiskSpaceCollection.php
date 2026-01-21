<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use MartinCamen\ArrCore\Concerns\ConvertsFileSize;
use MartinCamen\PhpFileSize\Enums\Unit;
use MartinCamen\PhpFileSize\FileSize;
use Traversable;

/**
 * Collection of disk space information from *arr APIs.
 *
 * This is a shared data structure used by both Radarr and Sonarr.
 *
 * @implements IteratorAggregate<int, DiskSpace>
 */
final class DiskSpaceCollection implements Countable, IteratorAggregate
{
    use ConvertsFileSize;

    /** @param array<int, DiskSpace> $disks */
    public function __construct(private array $disks = []) {}

    /** @param array<int, array<string, mixed>> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            array_map(
                DiskSpace::fromArray(...),
                $data,
            ),
        );
    }

    /** @return array<int, DiskSpace> */
    public function all(): array
    {
        return $this->disks;
    }

    public function count(): int
    {
        return count($this->disks);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function first(): ?DiskSpace
    {
        return $this->disks[0] ?? null;
    }

    public function last(): ?DiskSpace
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->disks[$this->count() - 1];
    }

    public function get(int $index): ?DiskSpace
    {
        return $this->disks[$index] ?? null;
    }

    /** @return Traversable<int, DiskSpace> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->disks);
    }

    /** @return array<int, array<string, mixed>> */
    public function toArray(): array
    {
        return array_map(
            static fn(DiskSpace $disk): array => $disk->toArray(),
            $this->disks,
        );
    }

    public function totalFreeSpace(): FileSize
    {
        $totalBytes = array_reduce(
            $this->disks,
            static fn(float $total, DiskSpace $disk): float => $total + $disk->freeSpace->getBytes(),
            0.0,
        );

        return FileSize::fromBytes($totalBytes);
    }

    public function totalSpace(): FileSize
    {
        $totalBytes = array_reduce(
            $this->disks,
            static fn(float $total, DiskSpace $disk): float => $total + $disk->totalSpace->getBytes(),
            0.0,
        );

        return FileSize::fromBytes($totalBytes);
    }

    public function totalUsedSpace(): FileSize
    {
        $usedBytes = $this->totalSpace()->getBytes() - $this->totalFreeSpace()->getBytes();

        return FileSize::fromBytes($usedBytes);
    }

    /**
     * Get total free space in specified unit.
     */
    public function totalFreeSpaceIn(Unit $unit, ?int $precision = 2): float
    {
        return $this->convertToUnit($this->totalFreeSpace(), $unit, $precision);
    }

    /**
     * Get total space in specified unit.
     */
    public function totalSpaceIn(Unit $unit, ?int $precision = 2): float
    {
        return $this->convertToUnit($this->totalSpace(), $unit, $precision);
    }

    /**
     * Get total used space in specified unit.
     */
    public function totalUsedSpaceIn(Unit $unit, ?int $precision = 2): float
    {
        return $this->convertToUnit($this->totalUsedSpace(), $unit, $precision);
    }

    public function totalUsedPercentage(): float
    {
        $totalBytes = $this->totalSpace()->getBytes();

        if ($totalBytes === 0.0) {
            return 0.0;
        }

        return round(($this->totalUsedSpace()->getBytes() / $totalBytes) * 100, 2);
    }

    public function totalFreePercentage(): float
    {
        return 100.0 - $this->totalUsedPercentage();
    }
}
