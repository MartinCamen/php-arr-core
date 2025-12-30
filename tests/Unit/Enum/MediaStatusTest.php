<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Enum;

use MartinCamen\ArrCore\Enum\MediaStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MediaStatusTest extends TestCase
{
    #[Test]
    public function hasExpectedCases(): void
    {
        $this->assertSame('unknown', MediaStatus::Unknown->value);
        $this->assertSame('downloading', MediaStatus::Downloading->value);
        $this->assertSame('available', MediaStatus::Available->value);
    }

    #[Test]
    #[DataProvider('activeStatusProvider')]
    public function identifiesActiveStatuses(MediaStatus $status, bool $expected): void
    {
        $this->assertSame($expected, $status->isActive());
    }

    /**
     * @return array<string, array{MediaStatus, bool}>
     */
    public static function activeStatusProvider(): array
    {
        return [
            'Queued is active'        => [MediaStatus::Queued, true],
            'Downloading is active'   => [MediaStatus::Downloading, true],
            'Available is not active' => [MediaStatus::Available, false],
            'Missing is not active'   => [MediaStatus::Missing, false],
        ];
    }

    #[Test]
    #[DataProvider('terminalStatusProvider')]
    public function identifiesTerminalStatuses(MediaStatus $status, bool $expected): void
    {
        $this->assertSame($expected, $status->isTerminal());
    }

    /**
     * @return array<string, array{MediaStatus, bool}>
     */
    public static function terminalStatusProvider(): array
    {
        return [
            'Available is terminal'       => [MediaStatus::Available, true],
            'Downloaded is terminal'      => [MediaStatus::Downloaded, true],
            'Failed is terminal'          => [MediaStatus::Failed, true],
            'Downloading is not terminal' => [MediaStatus::Downloading, false],
            'Missing is not terminal'     => [MediaStatus::Missing, false],
        ];
    }

    #[Test]
    public function identifiesNeedsAttention(): void
    {
        $this->assertTrue(MediaStatus::Failed->needsAttention());
        $this->assertTrue(MediaStatus::Missing->needsAttention());
        $this->assertTrue(MediaStatus::Unknown->needsAttention());
        $this->assertFalse(MediaStatus::Available->needsAttention());
    }

    #[Test]
    public function hasMediaChecks(): void
    {
        $this->assertTrue(MediaStatus::Available->hasMedia());
        $this->assertTrue(MediaStatus::Downloaded->hasMedia());
        $this->assertFalse(MediaStatus::Missing->hasMedia());
        $this->assertFalse(MediaStatus::Downloading->hasMedia());
    }

    #[Test]
    public function prioritiesAreOrdered(): void
    {
        $this->assertLessThan(
            MediaStatus::Available->priority(),
            MediaStatus::Failed->priority(),
        );
        $this->assertLessThan(
            MediaStatus::Missing->priority(),
            MediaStatus::Downloading->priority(),
        );
    }

    #[Test]
    public function providesLabels(): void
    {
        $this->assertSame('Downloading', MediaStatus::Downloading->label());
        $this->assertSame('Available', MediaStatus::Available->label());
    }

    #[Test]
    public function providesColorClasses(): void
    {
        $this->assertSame('red', MediaStatus::Failed->colorClass());
        $this->assertSame('green', MediaStatus::Available->colorClass());
        $this->assertSame('blue', MediaStatus::Downloading->colorClass());
    }
}
