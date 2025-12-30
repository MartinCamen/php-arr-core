<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\ValueObject;

use InvalidArgumentException;
use MartinCamen\ArrCore\ValueObject\ArrId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ArrIdTest extends TestCase
{
    #[Test]
    public function canBeCreatedFromInt(): void
    {
        $id = ArrId::fromInt(123);

        $this->assertSame(123, $id->value());
        $this->assertTrue($id->isInt());
        $this->assertFalse($id->isString());
    }

    #[Test]
    public function canBeCreatedFromString(): void
    {
        $id = ArrId::fromString('abc-123');

        $this->assertSame('abc-123', $id->value());
        $this->assertTrue($id->isString());
        $this->assertFalse($id->isInt());
    }

    #[Test]
    public function canBeCreatedFromEither(): void
    {
        $intId = ArrId::from(456);
        $stringId = ArrId::from('def-456');

        $this->assertSame(456, $intId->value());
        $this->assertSame('def-456', $stringId->value());
    }

    #[Test]
    public function throwsOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ArrId cannot be an empty string');

        ArrId::fromString('');
    }

    #[Test]
    public function throwsOnWhitespaceOnlyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ArrId::fromString('   ');
    }

    #[Test]
    public function canCompareEquality(): void
    {
        $id1 = ArrId::fromInt(123);
        $id2 = ArrId::fromInt(123);
        $id3 = ArrId::fromInt(456);
        $id4 = ArrId::fromString('123');

        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
        $this->assertFalse($id1->equals($id4));
    }

    #[Test]
    public function canBeConvertedToString(): void
    {
        $intId = ArrId::fromInt(123);
        $stringId = ArrId::fromString('abc');

        $this->assertSame('123', (string) $intId);
        $this->assertSame('abc', (string) $stringId);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $id = ArrId::fromInt(123);

        $this->assertSame(['id' => 123], $id->toArray());
    }
}
