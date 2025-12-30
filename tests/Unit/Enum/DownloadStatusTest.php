<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Enum;

use MartinCamen\ArrCore\Enum\DownloadStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DownloadStatusTest extends TestCase
{
    #[Test]
    public function hasExpectedCases(): void
    {
        $this->assertSame('downloading', DownloadStatus::Downloading->value);
        $this->assertSame('completed', DownloadStatus::Completed->value);
        $this->assertSame('failed', DownloadStatus::Failed->value);
    }

    #[Test]
    #[DataProvider('activeStatusProvider')]
    public function identifiesActiveStatuses(DownloadStatus $status, bool $expected): void
    {
        $this->assertSame($expected, $status->isActive());
    }

    /**
     * @return array<string, array{DownloadStatus, bool}>
     */
    public static function activeStatusProvider(): array
    {
        return [
            'Downloading is active'   => [DownloadStatus::Downloading, true],
            'Verifying is active'     => [DownloadStatus::Verifying, true],
            'Extracting is active'    => [DownloadStatus::Extracting, true],
            'Importing is active'     => [DownloadStatus::Importing, true],
            'Queued is not active'    => [DownloadStatus::Queued, false],
            'Completed is not active' => [DownloadStatus::Completed, false],
        ];
    }

    #[Test]
    public function identifiesTerminalStatuses(): void
    {
        $this->assertTrue(DownloadStatus::Completed->isTerminal());
        $this->assertTrue(DownloadStatus::Failed->isTerminal());
        $this->assertFalse(DownloadStatus::Downloading->isTerminal());
    }

    #[Test]
    public function identifiesErrors(): void
    {
        $this->assertTrue(DownloadStatus::Failed->isError());
        $this->assertTrue(DownloadStatus::Warning->isError());
        $this->assertFalse(DownloadStatus::Downloading->isError());
    }

    #[Test]
    public function identifiesWaiting(): void
    {
        $this->assertTrue(DownloadStatus::Queued->isWaiting());
        $this->assertTrue(DownloadStatus::Paused->isWaiting());
        $this->assertFalse(DownloadStatus::Downloading->isWaiting());
    }

    #[Test]
    public function identifiesPostProcessing(): void
    {
        $this->assertTrue(DownloadStatus::Verifying->isPostProcessing());
        $this->assertTrue(DownloadStatus::Extracting->isPostProcessing());
        $this->assertTrue(DownloadStatus::Importing->isPostProcessing());
        $this->assertFalse(DownloadStatus::Downloading->isPostProcessing());
    }

    #[Test]
    public function prioritiesAreOrdered(): void
    {
        $this->assertLessThan(
            DownloadStatus::Completed->priority(),
            DownloadStatus::Failed->priority(),
        );
    }

    #[Test]
    public function providesLabels(): void
    {
        $this->assertSame('Downloading', DownloadStatus::Downloading->label());
        $this->assertSame('Completed', DownloadStatus::Completed->label());
    }

    #[Test]
    public function providesColorClasses(): void
    {
        $this->assertSame('red', DownloadStatus::Failed->colorClass());
        $this->assertSame('green', DownloadStatus::Completed->colorClass());
        $this->assertSame('blue', DownloadStatus::Downloading->colorClass());
    }
}
