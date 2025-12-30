<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Domain\Download;

use MartinCamen\ArrCore\Domain\Download\DownloadItem;
use MartinCamen\ArrCore\Domain\Download\DownloadItemCollection;
use MartinCamen\ArrCore\Enum\DownloadStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\FileSize;
use MartinCamen\ArrCore\ValueObject\Progress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DownloadItemCollectionTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $item1 = $this->createItem(1, DownloadStatus::Downloading);
        $item2 = $this->createItem(2, DownloadStatus::Queued);

        $collection = new DownloadItemCollection($item1, $item2);

        $this->assertCount(2, $collection);
        $this->assertFalse($collection->isEmpty());
    }

    #[Test]
    public function canBeEmpty(): void
    {
        $collection = new DownloadItemCollection();

        $this->assertCount(0, $collection);
        $this->assertTrue($collection->isEmpty());
        $this->assertNull($collection->first());
    }

    #[Test]
    public function canGetFirst(): void
    {
        $item1 = $this->createItem(1, DownloadStatus::Downloading);
        $item2 = $this->createItem(2, DownloadStatus::Queued);

        $collection = new DownloadItemCollection($item1, $item2);

        $this->assertSame($item1, $collection->first());
    }

    #[Test]
    public function canFilterByStatus(): void
    {
        $downloading = $this->createItem(1, DownloadStatus::Downloading);
        $queued = $this->createItem(2, DownloadStatus::Queued);
        $failed = $this->createItem(3, DownloadStatus::Failed);

        $collection = new DownloadItemCollection($downloading, $queued, $failed);

        $this->assertCount(1, $collection->byStatus(DownloadStatus::Downloading));
        $this->assertCount(1, $collection->byStatus(DownloadStatus::Failed));
    }

    #[Test]
    public function canFilterBySource(): void
    {
        $sonarrItem = $this->createItem(1, DownloadStatus::Downloading, Service::Sonarr);
        $radarrItem = $this->createItem(2, DownloadStatus::Downloading, Service::Radarr);
        $nzbgetItem = $this->createItem(3, DownloadStatus::Downloading, Service::NZBGet);

        $collection = new DownloadItemCollection($sonarrItem, $radarrItem, $nzbgetItem);

        $this->assertCount(1, $collection->bySource(Service::Sonarr));
        $this->assertCount(1, $collection->bySource(Service::NZBGet));
    }

    #[Test]
    public function canGetActive(): void
    {
        $downloading = $this->createItem(1, DownloadStatus::Downloading);
        $verifying = $this->createItem(2, DownloadStatus::Verifying);
        $queued = $this->createItem(3, DownloadStatus::Queued);
        $completed = $this->createItem(4, DownloadStatus::Completed);

        $collection = new DownloadItemCollection($downloading, $verifying, $queued, $completed);
        $active = $collection->active();

        $this->assertCount(2, $active);
    }

    #[Test]
    public function canGetCompleted(): void
    {
        $downloading = $this->createItem(1, DownloadStatus::Downloading);
        $completed = $this->createItem(2, DownloadStatus::Completed);

        $collection = new DownloadItemCollection($downloading, $completed);

        $this->assertCount(1, $collection->completed());
    }

    #[Test]
    public function canGetFailed(): void
    {
        $downloading = $this->createItem(1, DownloadStatus::Downloading);
        $failed = $this->createItem(2, DownloadStatus::Failed);

        $collection = new DownloadItemCollection($downloading, $failed);

        $this->assertCount(1, $collection->failed());
    }

    #[Test]
    public function canGetWaiting(): void
    {
        $queued = $this->createItem(1, DownloadStatus::Queued);
        $paused = $this->createItem(2, DownloadStatus::Paused);
        $downloading = $this->createItem(3, DownloadStatus::Downloading);

        $collection = new DownloadItemCollection($queued, $paused, $downloading);

        $this->assertCount(2, $collection->waiting());
    }

    #[Test]
    public function canSortByPriority(): void
    {
        $completed = $this->createItem(1, DownloadStatus::Completed);
        $failed = $this->createItem(2, DownloadStatus::Failed);
        $downloading = $this->createItem(3, DownloadStatus::Downloading);

        $collection = new DownloadItemCollection($completed, $failed, $downloading);
        $sorted = $collection->sortByPriority();

        $items = $sorted->all();
        $this->assertSame(DownloadStatus::Failed, $items[0]->status);
        $this->assertSame(DownloadStatus::Downloading, $items[1]->status);
        $this->assertSame(DownloadStatus::Completed, $items[2]->status);
    }

    #[Test]
    public function calculatesTotalSize(): void
    {
        $item1 = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Item 1',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $item2 = new DownloadItem(
            id: ArrId::fromInt(2),
            name: 'Item 2',
            size: FileSize::fromGB(2),
            sizeRemaining: FileSize::fromGB(1),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $collection = new DownloadItemCollection($item1, $item2);

        $this->assertSame(3.0, $collection->totalSize()->gb());
        $this->assertSame(1.5, $collection->totalRemaining()->gb());
    }

    #[Test]
    public function calculatesTotalProgress(): void
    {
        $item1 = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Item 1',
            size: FileSize::fromGB(2),
            sizeRemaining: FileSize::fromGB(1),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $item2 = new DownloadItem(
            id: ArrId::fromInt(2),
            name: 'Item 2',
            size: FileSize::fromGB(2),
            sizeRemaining: FileSize::fromGB(0),
            progress: Progress::complete(),
            status: DownloadStatus::Completed,
            source: Service::NZBGet,
        );

        $collection = new DownloadItemCollection($item1, $item2);

        $this->assertEqualsWithDelta(75.0, $collection->totalProgress()->percentage(), 0.1);
    }

    #[Test]
    public function canMergeCollections(): void
    {
        $sonarrItem = $this->createItem(1, DownloadStatus::Downloading, Service::Sonarr);
        $radarrItem = $this->createItem(2, DownloadStatus::Downloading, Service::Radarr);

        $sonarrCollection = new DownloadItemCollection($sonarrItem);
        $radarrCollection = new DownloadItemCollection($radarrItem);

        $merged = $sonarrCollection->merge($radarrCollection);

        $this->assertCount(2, $merged);
    }

    #[Test]
    public function canBeIterated(): void
    {
        $item1 = $this->createItem(1, DownloadStatus::Downloading);
        $item2 = $this->createItem(2, DownloadStatus::Queued);

        $collection = new DownloadItemCollection($item1, $item2);

        $count = 0;
        foreach ($collection as $item) {
            $this->assertInstanceOf(DownloadItem::class, $item);
            $count++;
        }

        $this->assertSame(2, $count);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $item1 = $this->createItem(1, DownloadStatus::Downloading);
        $item2 = $this->createItem(2, DownloadStatus::Queued);

        $collection = new DownloadItemCollection($item1, $item2);
        $array = $collection->toArray();

        $this->assertArrayHasKey('items', $array);
        $this->assertArrayHasKey('count', $array);
        $this->assertArrayHasKey('total_size', $array);
        $this->assertArrayHasKey('total_progress', $array);
        $this->assertCount(2, $array['items']);
        $this->assertSame('1', $array['items'][0]['id']);
    }

    private function createItem(
        int $id,
        DownloadStatus $status,
        Service $source = Service::NZBGet,
    ): DownloadItem {
        return new DownloadItem(
            id: ArrId::fromInt($id),
            name: "Item {$id}",
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: $status,
            source: $source,
        );
    }
}
