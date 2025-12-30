<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\ValueObject;

use DateTimeImmutable;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\Timestamp;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TimestampTest extends TestCase
{
    #[Test]
    public function canCreateNow(): void
    {
        $before = time();
        $timestamp = Timestamp::now();
        $after = time();

        $this->assertGreaterThanOrEqual($before, $timestamp->unix());
        $this->assertLessThanOrEqual($after, $timestamp->unix());
    }

    #[Test]
    public function canCreateFromDateTime(): void
    {
        $dateTime = new DateTimeImmutable('2024-01-15 12:30:00');
        $timestamp = Timestamp::fromDateTime($dateTime);

        $this->assertSame($dateTime->getTimestamp(), $timestamp->unix());
    }

    #[Test]
    public function canCreateFromString(): void
    {
        $timestamp = Timestamp::fromString('2024-01-15T12:30:00+00:00');

        $this->assertSame('2024-01-15', $timestamp->format('Y-m-d'));
    }

    #[Test]
    public function canCreateFromUnix(): void
    {
        $unix = 1705322400;
        $timestamp = Timestamp::fromUnix($unix);

        $this->assertSame($unix, $timestamp->unix());
    }

    #[Test]
    public function canFormatToIso8601(): void
    {
        $timestamp = Timestamp::fromString('2024-01-15T12:30:00+00:00');

        $this->assertStringContainsString('2024-01-15', $timestamp->iso8601());
    }

    #[Test]
    public function canChangeTimezone(): void
    {
        $utc = Timestamp::fromString('2024-01-15T12:00:00+00:00');
        $la = $utc->inTimezone('America/Los_Angeles');

        $this->assertSame($utc->unix(), $la->unix());
    }

    #[Test]
    public function detectsPastTimestamps(): void
    {
        $past = Timestamp::fromUnix(time() - 3600);
        $future = Timestamp::fromUnix(time() + 3600);

        $this->assertTrue($past->isPast());
        $this->assertFalse($past->isFuture());
        $this->assertFalse($future->isPast());
        $this->assertTrue($future->isFuture());
    }

    #[Test]
    public function canCalculateDiff(): void
    {
        $t1 = Timestamp::fromUnix(1000);
        $t2 = Timestamp::fromUnix(2000);

        $diff = $t1->diffFrom($t2);

        $this->assertInstanceOf(Duration::class, $diff);
        $this->assertSame(1000, $diff->seconds());
    }

    #[Test]
    public function canCompareTimestamps(): void
    {
        $t1 = Timestamp::fromUnix(1000);
        $t2 = Timestamp::fromUnix(2000);
        $t3 = Timestamp::fromUnix(1000);

        $this->assertTrue($t1->isBefore($t2));
        $this->assertTrue($t2->isAfter($t1));
        $this->assertTrue($t1->equals($t3));
    }

    #[Test]
    public function canConvertToArray(): void
    {
        $timestamp = Timestamp::fromUnix(1705322400);
        $array = $timestamp->toArray();

        $this->assertArrayHasKey('unix', $array);
        $this->assertArrayHasKey('iso8601', $array);
        $this->assertSame(1705322400, $array['unix']);
    }
}
