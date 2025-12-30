<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Enum;

use MartinCamen\ArrCore\Enum\MediaType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MediaTypeTest extends TestCase
{
    #[Test]
    public function hasExpectedCases(): void
    {
        $this->assertSame('movie', MediaType::Movie->value);
        $this->assertSame('series', MediaType::Series->value);
        $this->assertSame('episode', MediaType::Episode->value);
    }

    #[Test]
    public function identifiesVideoContent(): void
    {
        $this->assertTrue(MediaType::Movie->isVideo());
        $this->assertTrue(MediaType::Series->isVideo());
        $this->assertTrue(MediaType::Episode->isVideo());
        $this->assertFalse(MediaType::Album->isVideo());
    }

    #[Test]
    public function identifiesAudioContent(): void
    {
        $this->assertTrue(MediaType::Artist->isAudio());
        $this->assertTrue(MediaType::Album->isAudio());
        $this->assertTrue(MediaType::Track->isAudio());
        $this->assertFalse(MediaType::Movie->isAudio());
    }

    #[Test]
    public function identifiesWrittenContent(): void
    {
        $this->assertTrue(MediaType::Book->isWritten());
        $this->assertTrue(MediaType::Author->isWritten());
        $this->assertFalse(MediaType::Movie->isWritten());
    }

    #[Test]
    public function identifiesContainers(): void
    {
        $this->assertTrue(MediaType::Series->isContainer());
        $this->assertTrue(MediaType::Season->isContainer());
        $this->assertTrue(MediaType::Album->isContainer());
        $this->assertFalse(MediaType::Movie->isContainer());
        $this->assertFalse(MediaType::Episode->isContainer());
    }

    #[Test]
    public function providesLabels(): void
    {
        $this->assertSame('Movie', MediaType::Movie->label());
        $this->assertSame('Movies', MediaType::Movie->labelPlural());
        $this->assertSame('Series', MediaType::Series->label());
        $this->assertSame('Series', MediaType::Series->labelPlural());
    }
}
