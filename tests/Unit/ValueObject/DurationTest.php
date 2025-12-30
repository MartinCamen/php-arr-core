<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\ValueObject;

use DateInterval;
use InvalidArgumentException;
use MartinCamen\ArrCore\ValueObject\Duration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DurationTest extends TestCase
{
    #[Test]
    public function canCreateZero(): void
    {
        $duration = Duration::zero();

        $this->assertSame(0, $duration->seconds());
        $this->assertTrue($duration->isZero());
    }

    #[Test]
    public function canCreateFromSeconds(): void
    {
        $duration = Duration::fromSeconds(90);

        $this->assertSame(90, $duration->seconds());
        $this->assertSame(1.5, $duration->minutes());
    }

    #[Test]
    public function canCreateFromMinutes(): void
    {
        $duration = Duration::fromMinutes(2.5);

        $this->assertSame(150, $duration->seconds());
    }

    #[Test]
    public function canCreateFromHours(): void
    {
        $duration = Duration::fromHours(1.5);

        $this->assertSame(5400, $duration->seconds());
    }

    #[Test]
    public function canCreateFromDateInterval(): void
    {
        $interval = new DateInterval('PT1H30M');
        $duration = Duration::fromDateInterval($interval);

        $this->assertSame(5400, $duration->seconds());
    }

    #[Test]
    public function throwsOnNegativeSeconds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Duration::fromSeconds(-1);
    }

    #[Test]
    #[DataProvider('formattedProvider')]
    public function formatsCorrectly(int $seconds, string $expected): void
    {
        $duration = Duration::fromSeconds($seconds);

        $this->assertSame($expected, $duration->formatted());
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function formattedProvider(): array
    {
        return [
            'zero'                  => [0, '0s'],
            'seconds only'          => [45, '45s'],
            'minutes and seconds'   => [125, '2m 5s'],
            'hours minutes seconds' => [3725, '1h 2m 5s'],
            'days'                  => [90061, '1d 1h 1m 1s'],
        ];
    }

    #[Test]
    public function canConvertToDateInterval(): void
    {
        $duration = Duration::fromSeconds(3665);
        $interval = $duration->toDateInterval();

        $this->assertInstanceOf(DateInterval::class, $interval);
    }

    #[Test]
    public function canAddDurations(): void
    {
        $d1 = Duration::fromMinutes(30);
        $d2 = Duration::fromMinutes(45);
        $result = $d1->add($d2);

        $this->assertSame(75.0, $result->minutes());
    }

    #[Test]
    public function canSubtractDurations(): void
    {
        $d1 = Duration::fromHours(2);
        $d2 = Duration::fromMinutes(30);
        $result = $d1->subtract($d2);

        $this->assertSame(1.5, $result->hours());
    }

    #[Test]
    public function subtractClampsToZero(): void
    {
        $d1 = Duration::fromMinutes(10);
        $d2 = Duration::fromMinutes(20);
        $result = $d1->subtract($d2);

        $this->assertTrue($result->isZero());
    }

    #[Test]
    public function canCompareDurations(): void
    {
        $short = Duration::fromMinutes(5);
        $long = Duration::fromHours(1);

        $this->assertTrue($long->isGreaterThan($short));
        $this->assertTrue($short->equals(Duration::fromSeconds(300)));
    }
}
