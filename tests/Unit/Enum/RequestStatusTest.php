<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Enum;

use MartinCamen\ArrCore\Enum\RequestStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RequestStatusTest extends TestCase
{
    #[Test]
    public function hasExpectedCases(): void
    {
        $this->assertSame('pending', RequestStatus::Pending->value);
        $this->assertSame('approved', RequestStatus::Approved->value);
        $this->assertSame('fulfilled', RequestStatus::Fulfilled->value);
    }

    #[Test]
    public function identifiesPending(): void
    {
        $this->assertTrue(RequestStatus::Pending->isPending());
        $this->assertFalse(RequestStatus::Approved->isPending());
    }

    #[Test]
    public function identifiesTerminal(): void
    {
        $this->assertTrue(RequestStatus::Rejected->isTerminal());
        $this->assertTrue(RequestStatus::Fulfilled->isTerminal());
        $this->assertTrue(RequestStatus::Failed->isTerminal());
        $this->assertFalse(RequestStatus::Pending->isTerminal());
        $this->assertFalse(RequestStatus::Approved->isTerminal());
    }

    #[Test]
    public function identifiesSuccessful(): void
    {
        $this->assertTrue(RequestStatus::Approved->isSuccessful());
        $this->assertTrue(RequestStatus::Fulfilled->isSuccessful());
        $this->assertFalse(RequestStatus::Pending->isSuccessful());
        $this->assertFalse(RequestStatus::Rejected->isSuccessful());
    }

    #[Test]
    public function identifiesNeedsAction(): void
    {
        $this->assertTrue(RequestStatus::Pending->needsAction());
        $this->assertFalse(RequestStatus::Approved->needsAction());
    }

    #[Test]
    public function providesLabels(): void
    {
        $this->assertSame('Pending', RequestStatus::Pending->label());
        $this->assertSame('Fulfilled', RequestStatus::Fulfilled->label());
    }

    #[Test]
    public function providesColorClasses(): void
    {
        $this->assertSame('yellow', RequestStatus::Pending->colorClass());
        $this->assertSame('green', RequestStatus::Fulfilled->colorClass());
        $this->assertSame('red', RequestStatus::Rejected->colorClass());
    }
}
