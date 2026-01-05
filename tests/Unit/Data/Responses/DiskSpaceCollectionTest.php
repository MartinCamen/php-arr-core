<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Data\Responses;

use MartinCamen\ArrCore\Data\Responses\DiskSpace;
use MartinCamen\ArrCore\Data\Responses\DiskSpaceCollection;
use MartinCamen\PhpFileSize\Enums\Unit;
use MartinCamen\PhpFileSize\FileSize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DiskSpaceCollectionTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $this->assertCount(2, $collection);
        $this->assertFalse($collection->isEmpty());
    }

    #[Test]
    public function canBeEmpty(): void
    {
        $collection = new DiskSpaceCollection([]);

        $this->assertCount(0, $collection);
        $this->assertTrue($collection->isEmpty());
        $this->assertNull($collection->first());
        $this->assertNull($collection->last());
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $collection = DiskSpaceCollection::fromArray([
            [
                'path'       => '/mnt/disk1',
                'label'      => 'Disk 1',
                'freeSpace'  => FileSize::fromGigabytes(500)->toBytes(),
                'totalSpace' => FileSize::fromGigabytes(1024)->toBytes(),
            ],
            [
                'path'       => '/mnt/disk2',
                'label'      => 'Disk 2',
                'freeSpace'  => FileSize::fromGigabytes(250)->toBytes(),
                'totalSpace' => FileSize::fromGigabytes(512)->toBytes(),
            ],
        ]);

        $this->assertCount(2, $collection);
        $this->assertSame('/mnt/disk1', $collection->first()?->path);
        $this->assertSame('/mnt/disk2', $collection->last()?->path);
    }

    #[Test]
    public function canGetFirst(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $this->assertSame($disk1, $collection->first());
    }

    #[Test]
    public function canGetLast(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $this->assertSame($disk2, $collection->last());
    }

    #[Test]
    public function canGetByIndex(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $this->assertSame($disk1, $collection->get(0));
        $this->assertSame($disk2, $collection->get(1));
        $this->assertNull($collection->get(2));
    }

    #[Test]
    public function canGetAll(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $all = $collection->all();

        $this->assertCount(2, $all);
        $this->assertSame($disk1, $all[0]);
        $this->assertSame($disk2, $all[1]);
    }

    #[Test]
    public function canBeIterated(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $count = 0;
        foreach ($collection as $disk) {
            $this->assertInstanceOf(DiskSpace::class, $disk);
            $count++;
        }

        $this->assertSame(2, $count);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);
        $array = $collection->toArray();

        $this->assertCount(2, $array);
        $this->assertSame('/mnt/disk1', $array[0]['path']);
        $this->assertSame('/mnt/disk2', $array[1]['path']);
    }

    #[Test]
    public function calculatesTotalFreeSpace(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000); // 500 GB free
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);  // 250 GB free

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $totalFree = $collection->totalFreeSpace();

        $this->assertInstanceOf(FileSize::class, $totalFree);
        $this->assertEqualsWithDelta(750.0, $totalFree->toGigabytes(), 1.0);
    }

    #[Test]
    public function calculatesTotalSpace(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000); // 1 TB total
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);  // 500 GB total

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $total = $collection->totalSpace();

        $this->assertInstanceOf(FileSize::class, $total);
        $this->assertEqualsWithDelta(1500.0, $total->toGigabytes(), 1.0);
    }

    #[Test]
    public function calculatesTotalUsedSpace(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000); // 500 GB used
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);  // 250 GB used

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $totalUsed = $collection->totalUsedSpace();

        $this->assertInstanceOf(FileSize::class, $totalUsed);
        $this->assertEqualsWithDelta(750.0, $totalUsed->toGigabytes(), 1.0);
    }

    #[Test]
    public function getsTotalFreeSpaceInUnit(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $this->assertEqualsWithDelta(750.0, $collection->totalFreeSpaceIn(Unit::GigaByte), 1.0);
        $this->assertEqualsWithDelta(0.73, $collection->totalFreeSpaceIn(Unit::TeraByte), 0.1);
    }

    #[Test]
    public function getsTotalSpaceInUnit(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $this->assertEqualsWithDelta(1500.0, $collection->totalSpaceIn(Unit::GigaByte), 1.0);
        $this->assertEqualsWithDelta(1.46, $collection->totalSpaceIn(Unit::TeraByte), 0.1);
    }

    #[Test]
    public function getsTotalUsedSpaceInUnit(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $this->assertEqualsWithDelta(750.0, $collection->totalUsedSpaceIn(Unit::GigaByte), 1.0);
    }

    #[Test]
    public function calculatesTotalUsedPercentage(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000); // 50% used
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);  // 50% used

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        // Total: 1500 GB, Used: 750 GB = 50%
        $this->assertEqualsWithDelta(50.0, $collection->totalUsedPercentage(), 1.0);
    }

    #[Test]
    public function calculatesTotalFreePercentage(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        // Total: 1500 GB, Free: 750 GB = 50%
        $this->assertEqualsWithDelta(50.0, $collection->totalFreePercentage(), 1.0);
    }

    #[Test]
    public function handlesEmptyCollectionForCalculations(): void
    {
        $collection = new DiskSpaceCollection([]);

        $this->assertTrue($collection->totalFreeSpace()->isZero());
        $this->assertTrue($collection->totalSpace()->isZero());
        $this->assertTrue($collection->totalUsedSpace()->isZero());
        $this->assertSame(0.0, $collection->totalUsedPercentage());
        $this->assertSame(100.0, $collection->totalFreePercentage());
    }

    #[Test]
    public function respectsPrecisionParameter(): void
    {
        $disk1 = $this->createDisk('/mnt/disk1', 500, 1_000);
        $disk2 = $this->createDisk('/mnt/disk2', 250, 500);

        $collection = new DiskSpaceCollection([$disk1, $disk2]);

        $precisionZero = $collection->totalFreeSpaceIn(Unit::GigaByte, 0);
        $precisionTwo = $collection->totalFreeSpaceIn(Unit::GigaByte, 2);

        $this->assertSame(floor($precisionTwo), $precisionZero);
    }

    private function createDisk(string $path, float $freeGb, float $totalGb): DiskSpace
    {
        return new DiskSpace(
            path: $path,
            label: basename($path),
            freeSpace: FileSize::fromGigabytes($freeGb),
            totalSpace: FileSize::fromGigabytes($totalGb),
        );
    }
}
