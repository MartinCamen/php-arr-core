<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

use ArrayIterator;
use Countable;
use IteratorAggregate;
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

    public function totalFreeSpace(): int
    {
        $total = 0;

        foreach ($this->disks as $disk) {
            $total += $disk->freeSpace;
        }

        return $total;
    }

    public function totalSpace(): int
    {
        $total = 0;

        foreach ($this->disks as $disk) {
            $total += $disk->totalSpace;
        }

        return $total;
    }

    public function totalUsedSpace(): int
    {
        return $this->totalSpace() - $this->totalFreeSpace();
    }

    public function totalFreeSpaceGb(): float
    {
        return (new FileSize($this->totalFreeSpace()))->precision(2)->toGigabytes();
    }

    public function totalSpaceGb(): float
    {
        return (new FileSize($this->totalSpace()))->precision(2)->toGigabytes();
    }

    public function totalUsedSpaceGb(): float
    {
        return (new FileSize($this->totalUsedSpace()))->precision(2)->toGigabytes();
    }
}
