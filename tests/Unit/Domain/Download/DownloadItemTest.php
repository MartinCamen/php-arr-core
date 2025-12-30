<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Domain\Download;

use MartinCamen\ArrCore\Domain\Download\DownloadItem;
use MartinCamen\ArrCore\Enum\DownloadStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\FileSize;
use MartinCamen\ArrCore\ValueObject\Progress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DownloadItemTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $item = new DownloadItem(
            id: ArrId::fromInt(123),
            name: 'Test.Download.2024',
            size: FileSize::fromGB(4.5),
            sizeRemaining: FileSize::fromGB(2.0),
            progress: Progress::fromPercentage(55.6),
            status: DownloadStatus::Downloading,
            source: Service::Sonarr,
        );

        $this->assertSame(123, $item->id->value());
        $this->assertSame('Test.Download.2024', $item->name);
        $this->assertSame(DownloadStatus::Downloading, $item->status);
        $this->assertSame(Service::Sonarr, $item->source);
    }

    #[Test]
    public function detectsActiveDownloads(): void
    {
        $downloading = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Downloading',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $queued = new DownloadItem(
            id: ArrId::fromInt(2),
            name: 'Queued',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(1),
            progress: Progress::zero(),
            status: DownloadStatus::Queued,
            source: Service::NZBGet,
        );

        $this->assertTrue($downloading->isActive());
        $this->assertFalse($queued->isActive());
    }

    #[Test]
    public function detectsWaitingDownloads(): void
    {
        $queued = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Queued',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(1),
            progress: Progress::zero(),
            status: DownloadStatus::Queued,
            source: Service::NZBGet,
        );

        $paused = new DownloadItem(
            id: ArrId::fromInt(2),
            name: 'Paused',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Paused,
            source: Service::NZBGet,
        );

        $downloading = new DownloadItem(
            id: ArrId::fromInt(3),
            name: 'Downloading',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $this->assertTrue($queued->isWaiting());
        $this->assertTrue($paused->isWaiting());
        $this->assertFalse($downloading->isWaiting());
    }

    #[Test]
    public function detectsErrors(): void
    {
        $failed = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Failed',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(1),
            progress: Progress::zero(),
            status: DownloadStatus::Failed,
            source: Service::NZBGet,
        );

        $withMessage = new DownloadItem(
            id: ArrId::fromInt(2),
            name: 'Has Error',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
            errorMessage: 'Something went wrong',
        );

        $healthy = new DownloadItem(
            id: ArrId::fromInt(3),
            name: 'Healthy',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $this->assertTrue($failed->hasError());
        $this->assertTrue($withMessage->hasError());
        $this->assertFalse($healthy->hasError());
    }

    #[Test]
    public function calculatesDownloadedSize(): void
    {
        $item = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Test',
            size: FileSize::fromGB(4),
            sizeRemaining: FileSize::fromGB(1),
            progress: Progress::fromPercentage(75),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $downloaded = $item->downloadedSize();

        $this->assertSame(3.0, $downloaded->gb());
    }

    #[Test]
    public function detectsComplete(): void
    {
        $completed = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Completed',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::zero(),
            progress: Progress::complete(),
            status: DownloadStatus::Completed,
            source: Service::NZBGet,
        );

        $inProgress = new DownloadItem(
            id: ArrId::fromInt(2),
            name: 'In Progress',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $this->assertTrue($completed->isComplete());
        $this->assertFalse($inProgress->isComplete());
    }

    #[Test]
    public function getsDisplayTitle(): void
    {
        $withMediaTitle = new DownloadItem(
            id: ArrId::fromInt(1),
            name: 'Show.S01E01.720p',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::Sonarr,
            mediaTitle: 'The Show',
        );

        $withoutMediaTitle = new DownloadItem(
            id: ArrId::fromInt(2),
            name: 'Show.S01E01.720p',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::Sonarr,
        );

        $this->assertSame('The Show', $withMediaTitle->displayTitle());
        $this->assertSame('Show.S01E01.720p', $withoutMediaTitle->displayTitle());
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $item = DownloadItem::fromArray([
            'id'             => 123,
            'name'           => 'Test.Download',
            'size'           => 1073741824, // 1 GB
            'size_remaining' => 536870912, // 0.5 GB
            'progress'       => 50,
            'status'         => 'downloading',
            'source'         => 'nzbget',
            'eta'            => 3600,
        ]);

        $this->assertSame('Test.Download', $item->name);
        $this->assertSame(DownloadStatus::Downloading, $item->status);
        $this->assertInstanceOf(Duration::class, $item->eta);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $item = new DownloadItem(
            id: ArrId::fromInt(123),
            name: 'Test.Download',
            size: FileSize::fromGB(1),
            sizeRemaining: FileSize::fromGB(0.5),
            progress: Progress::fromPercentage(50),
            status: DownloadStatus::Downloading,
            source: Service::NZBGet,
        );

        $array = $item->toArray();

        $this->assertSame('123', $array['id']);
        $this->assertSame('Test.Download', $array['name']);
        $this->assertSame('downloading', $array['status']);
        $this->assertSame('nzbget', $array['source']);
    }
}
