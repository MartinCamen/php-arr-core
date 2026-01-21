<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Data\Responses;

use MartinCamen\ArrCore\Data\Responses\DiskSpace;
use MartinCamen\PhpFileSize\Enums\Unit;
use MartinCamen\PhpFileSize\FileSize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DiskSpaceTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromGigabytes(500),
            totalSpace: FileSize::fromTerabytes(2),
        );

        $this->assertSame('/mnt/media', $disk->path);
        $this->assertSame('Media Drive', $disk->label);
        $this->assertInstanceOf(FileSize::class, $disk->freeSpace);
        $this->assertInstanceOf(FileSize::class, $disk->totalSpace);
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $disk = DiskSpace::fromArray([
            'path'       => '/mnt/media',
            'label'      => 'Media Drive',
            'freeSpace'  => FileSize::fromGigabytes(500)->toBytes(),
            'totalSpace' => FileSize::fromTerabytes(2)->toBytes(),
        ]);

        $this->assertSame('/mnt/media', $disk->path);
        $this->assertSame('Media Drive', $disk->label);
        $this->assertEqualsWithDelta(500.0, $disk->freeSpace->toGigabytes(), 1.0);
        $this->assertEqualsWithDelta(2.0, $disk->totalSpace->toTerabytes(), 0.1);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: $freeSpace = FileSize::fromBytes($freeSpaceBytes = 536870912000),
            totalSpace: FileSize::fromBytes($totalSpaceBytes = 2199023255552),
        );

        $array = $disk->toArray();

        $this->assertSame('/mnt/media', $array['path']);
        $this->assertSame('Media Drive', $array['label']);
        $this->assertSame($freeSpaceBytes, $array['free_space']);
        $this->assertSame($totalSpaceBytes, $array['total_space']);
    }

    #[Test]
    public function calculatesUsedSpace(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromGigabytes(500),
            totalSpace: FileSize::fromTerabytes(2),
        );

        $usedSpace = $disk->usedSpace();

        $this->assertInstanceOf(FileSize::class, $usedSpace);
        // 2 TB - 500 GB = ~1.5 TB
        $this->assertEqualsWithDelta(1.5, $usedSpace->toTerabytes(), 0.1);
    }

    #[Test]
    public function calculatesUsedPercentage(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromGigabytes(250),
            totalSpace: FileSize::fromTerabytes(1),
        );

        // 750 GB used / 1 TB = 75%
        $this->assertEqualsWithDelta(75.0, $disk->usedPercentage(), 1.0);
    }

    #[Test]
    public function calculatesFreePercentage(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromGigabytes(250),
            totalSpace: FileSize::fromTerabytes(1),
        );

        // 250 GB free / 1 TB = 25%
        $this->assertEqualsWithDelta(25.0, $disk->freePercentage(), 1.0);
    }

    #[Test]
    public function handlesZeroTotalSpace(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/empty',
            label: 'Empty',
            freeSpace: FileSize::fromBytes(0),
            totalSpace: FileSize::fromBytes(0),
        );

        $this->assertSame(0.0, $disk->usedPercentage());
        $this->assertSame(100.0, $disk->freePercentage());
    }

    #[Test]
    public function getsFreeSpaceInUnit(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromGigabytes(500),
            totalSpace: FileSize::fromTerabytes(2),
        );

        $this->assertEqualsWithDelta(500.0, $disk->freeSpaceIn(Unit::GigaByte), 1.0);
        $this->assertEqualsWithDelta(512_000.0, $disk->freeSpaceIn(Unit::MegaByte), 100.0);
        $this->assertEqualsWithDelta(0.49, $disk->freeSpaceIn(Unit::TeraByte), 0.1);
    }

    #[Test]
    public function getsTotalSpaceInUnit(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromGigabytes(500),
            totalSpace: FileSize::fromTerabytes(2),
        );

        $this->assertEqualsWithDelta(2.0, $disk->totalSpaceIn(Unit::TeraByte), 0.1);
        $this->assertEqualsWithDelta(2_048.0, $disk->totalSpaceIn(Unit::GigaByte), 10.0);
    }

    #[Test]
    public function getsUsedSpaceInUnit(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromGigabytes(500),
            totalSpace: FileSize::fromTerabytes(2),
        );

        // 2 TB - 500 GB = ~1548 GB
        $this->assertEqualsWithDelta(1_548.0, $disk->usedSpaceIn(Unit::GigaByte), 10.0);
    }

    #[Test]
    public function respectsPrecisionParameter(): void
    {
        $disk = new DiskSpace(
            path: '/mnt/media',
            label: 'Media Drive',
            freeSpace: FileSize::fromBytes(536870912000),
            totalSpace: FileSize::fromBytes(2199023255552),
        );

        $precisionZero = $disk->freeSpaceIn(Unit::GigaByte, 0);
        $precisionTwo = $disk->freeSpaceIn(Unit::GigaByte, 2);

        $this->assertSame(floor($precisionTwo), $precisionZero);
    }
}
