<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\ValueObject;

use MartinCamen\ArrCore\ValueObject\Progress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProgressTest extends TestCase
{
    #[Test]
    public function canCreateZero(): void
    {
        $progress = Progress::zero();

        $this->assertSame(0.0, $progress->ratio());
        $this->assertSame(0.0, $progress->percentage());
        $this->assertTrue($progress->isZero());
        $this->assertFalse($progress->isComplete());
    }

    #[Test]
    public function canCreateComplete(): void
    {
        $progress = Progress::complete();

        $this->assertSame(1.0, $progress->ratio());
        $this->assertSame(100.0, $progress->percentage());
        $this->assertFalse($progress->isZero());
        $this->assertTrue($progress->isComplete());
    }

    #[Test]
    public function canCreateFromRatio(): void
    {
        $progress = Progress::fromRatio(0.5);

        $this->assertSame(0.5, $progress->ratio());
        $this->assertSame(50.0, $progress->percentage());
    }

    #[Test]
    public function clampsRatioToValidRange(): void
    {
        $low = Progress::fromRatio(-0.5);
        $high = Progress::fromRatio(1.5);

        $this->assertSame(0.0, $low->ratio());
        $this->assertSame(1.0, $high->ratio());
    }

    #[Test]
    public function canCreateFromPercentage(): void
    {
        $progress = Progress::fromPercentage(75.0);

        $this->assertSame(0.75, $progress->ratio());
        $this->assertSame(75.0, $progress->percentage());
    }

    #[Test]
    public function canCreateFromFraction(): void
    {
        $progress = Progress::fromFraction(3, 4);

        $this->assertSame(0.75, $progress->ratio());
    }

    #[Test]
    public function handlesDivisionByZeroInFraction(): void
    {
        $progress = Progress::fromFraction(5, 0);

        $this->assertTrue($progress->isZero());
    }

    #[Test]
    public function calculatesRemaining(): void
    {
        $progress = Progress::fromPercentage(40.0);

        $this->assertSame(60.0, $progress->remaining());
    }

    #[Test]
    public function formatsCorrectly(): void
    {
        $progress = Progress::fromPercentage(42.567);

        $this->assertSame('42.6%', $progress->formatted());
        $this->assertSame('42.57%', $progress->formatted(2));
    }

    #[Test]
    public function canCompareEquality(): void
    {
        $p1 = Progress::fromPercentage(50.0);
        $p2 = Progress::fromRatio(0.5);
        $p3 = Progress::fromPercentage(60.0);

        $this->assertTrue($p1->equals($p2));
        $this->assertFalse($p1->equals($p3));
    }

    #[Test]
    public function canConvertToArray(): void
    {
        $progress = Progress::fromPercentage(50.0);
        $array = $progress->toArray();

        $this->assertArrayHasKey('ratio', $array);
        $this->assertArrayHasKey('percentage', $array);
        $this->assertArrayHasKey('formatted', $array);
    }
}
