<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\ValueObject;

use InvalidArgumentException;
use MartinCamen\ArrCore\ValueObject\FileSize;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileSizeTest extends TestCase
{
    #[Test]
    public function canCreateZero(): void
    {
        $size = FileSize::zero();

        $this->assertSame(0, $size->bytes());
        $this->assertTrue($size->isZero());
    }

    #[Test]
    public function canCreateFromBytes(): void
    {
        $size = FileSize::fromBytes(1024);

        $this->assertSame(1024, $size->bytes());
        $this->assertSame(1.0, $size->kb());
    }

    #[Test]
    public function canCreateFromKB(): void
    {
        $size = FileSize::fromKB(1.5);

        $this->assertSame(1536, $size->bytes());
    }

    #[Test]
    public function canCreateFromMB(): void
    {
        $size = FileSize::fromMB(1.0);

        $this->assertSame(1024 * 1024, $size->bytes());
    }

    #[Test]
    public function canCreateFromGB(): void
    {
        $size = FileSize::fromGB(2.5);

        $this->assertEqualsWithDelta(2.5, $size->gb(), 0.001);
    }

    #[Test]
    public function canCreateFromTB(): void
    {
        $size = FileSize::fromTB(1.0);

        $this->assertSame(1024 * 1024 * 1024 * 1024, $size->bytes());
    }

    #[Test]
    public function throwsOnNegativeBytes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('FileSize cannot be negative');

        FileSize::fromBytes(-1);
    }

    #[Test]
    #[DataProvider('formattedProvider')]
    public function formatsCorrectly(int $bytes, string $expected): void
    {
        $size = FileSize::fromBytes($bytes);

        $this->assertSame($expected, $size->formatted());
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function formattedProvider(): array
    {
        return [
            'bytes'     => [500, '500 B'],
            'kilobytes' => [1536, '1.5 KB'],
            'megabytes' => [1572864, '1.5 MB'],
            'gigabytes' => [2684354560, '2.5 GB'],
            'terabytes' => [1099511627776, '1 TB'],
        ];
    }

    #[Test]
    public function canAddSizes(): void
    {
        $size1 = FileSize::fromMB(1);
        $size2 = FileSize::fromMB(2);
        $result = $size1->add($size2);

        $this->assertSame(3.0, $result->mb());
    }

    #[Test]
    public function canSubtractSizes(): void
    {
        $size1 = FileSize::fromMB(3);
        $size2 = FileSize::fromMB(1);
        $result = $size1->subtract($size2);

        $this->assertSame(2.0, $result->mb());
    }

    #[Test]
    public function subtractClampsToZero(): void
    {
        $size1 = FileSize::fromMB(1);
        $size2 = FileSize::fromMB(2);
        $result = $size1->subtract($size2);

        $this->assertSame(0, $result->bytes());
    }

    #[Test]
    public function canCompareSizes(): void
    {
        $small = FileSize::fromMB(1);
        $large = FileSize::fromGB(1);

        $this->assertTrue($large->isGreaterThan($small));
        $this->assertTrue($small->isLessThan($large));
        $this->assertFalse($small->equals($large));
        $this->assertTrue($small->equals(FileSize::fromMB(1)));
    }

    #[Test]
    public function canConvertToArray(): void
    {
        $size = FileSize::fromGB(1.5);
        $array = $size->toArray();

        $this->assertArrayHasKey('bytes', $array);
        $this->assertArrayHasKey('formatted', $array);
        $this->assertSame('1.5 GB', $array['formatted']);
    }
}
