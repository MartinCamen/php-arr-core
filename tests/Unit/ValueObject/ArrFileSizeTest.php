<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\ValueObject;

use MartinCamen\ArrCore\ValueObject\ArrFileSize;
use MartinCamen\PhpFileSize\Enums\Unit;
use MartinCamen\PhpFileSize\Exceptions\NegativeValueException;
use MartinCamen\PhpFileSize\FileSize;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ArrFileSizeTest extends TestCase
{
    #[Test]
    public function canCreateZero(): void
    {
        $size = ArrFileSize::zero();

        $this->assertSame(0.0, $size->getBytes());
        $this->assertTrue($size->isZero());
    }

    #[Test]
    public function canCreateFromBytes(): void
    {
        $size = ArrFileSize::fromBytes(1_024);

        $this->assertSame(1024.0, $size->getBytes());
        $this->assertSame(1.0, $size->toKilobytes());
    }

    #[Test]
    public function canCreateFromKilobytes(): void
    {
        $size = ArrFileSize::fromKilobytes(1.5);

        $this->assertSame(1536.0, $size->getBytes());
    }

    #[Test]
    public function canCreateFromMegabytes(): void
    {
        $size = ArrFileSize::fromMegabytes(1.0);

        $this->assertSame(1_024.0 * 1_024, $size->getBytes());
    }

    #[Test]
    public function canCreateFromGigabytes(): void
    {
        $size = ArrFileSize::fromGigabytes(2.5);

        $this->assertEqualsWithDelta(2.5, $size->toGigabytes(), 0.001);
    }

    #[Test]
    public function canCreateFromTerabytes(): void
    {
        $size = ArrFileSize::fromTerabytes(1.0);

        $this->assertSame(1_024.0 * 1_024 * 1_024 * 1_024, $size->getBytes());
    }

    #[Test]
    public function throwsOnNegativeBytes(): void
    {
        $this->expectException(NegativeValueException::class);

        ArrFileSize::fromBytes(-1);
    }

    #[Test]
    #[DataProvider('formattedProvider')]
    public function formatsCorrectly(int $bytes, string $expected): void
    {
        $size = ArrFileSize::fromBytes($bytes);

        $this->assertSame($expected, $size->format());
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function formattedProvider(): array
    {
        return [
            'bytes'     => [500, '500.00 Bytes'],
            'kilobytes' => [1536, '1.50 Kilobytes'],
            'megabytes' => [1572864, '1.50 Megabytes'],
            'gigabytes' => [2684354560, '2.50 Gigabytes'],
            'terabytes' => [1099511627776, '1.00 Terabytes'],
        ];
    }

    #[Test]
    public function canAddSizes(): void
    {
        $size1 = ArrFileSize::fromMegabytes(1);
        $result = $size1->addMegabytes(2)->evaluate();

        $this->assertSame(3.0, $result->toMegabytes());
    }

    #[Test]
    public function canSubtractSizes(): void
    {
        $size1 = ArrFileSize::fromMegabytes(3);
        $result = $size1->subMegabytes(1)->evaluate();

        $this->assertSame(2.0, $result->toMegabytes());
    }

    #[Test]
    public function canCompareSizes(): void
    {
        $small = ArrFileSize::fromMegabytes(1);
        $large = ArrFileSize::fromGigabytes(1);

        $this->assertTrue($large->greaterThan($small->getBytes(), Unit::Byte));
        $this->assertTrue($small->lessThan($large->getBytes(), Unit::Byte));
        $this->assertFalse($small->equals($large->getBytes(), Unit::Byte));
        $this->assertTrue($small->equals(1, Unit::MegaByte));
    }

    #[Test]
    public function canConvertToArray(): void
    {
        $size = ArrFileSize::fromGigabytes(1.5);
        $array = $size->toArray();

        $this->assertArrayHasKey('bytes', $array);
        $this->assertArrayHasKey('formatted', $array);
        $this->assertIsInt($array['bytes']);
        $this->assertIsString($array['formatted']);
    }

    #[Test]
    public function instanceOfFileSize(): void
    {
        $size = ArrFileSize::fromBytes(1_024);

        $this->assertInstanceOf(FileSize::class, $size);
    }
}
