<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use MartinCamen\ArrCore\Contract\Arrayable;

final readonly class Timestamp implements Arrayable
{
    private function __construct(
        private DateTimeImmutable $dateTime,
    ) {}

    public static function now(): self
    {
        return new self(new DateTimeImmutable());
    }

    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        if ($dateTime instanceof DateTimeImmutable) {
            return new self($dateTime);
        }

        return new self(DateTimeImmutable::createFromInterface($dateTime));
    }

    public static function fromString(string $dateString): self
    {
        $dateTime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dateString);

        if ($dateTime === false) {
            $dateTime = new DateTimeImmutable($dateString);
        }

        return new self($dateTime);
    }

    public static function fromUnix(int $timestamp): self
    {
        $dateTime = (new DateTimeImmutable())->setTimestamp($timestamp);

        return new self($dateTime);
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function unix(): int
    {
        return $this->dateTime->getTimestamp();
    }

    public function iso8601(): string
    {
        return $this->dateTime->format(DateTimeInterface::ATOM);
    }

    public function format(string $format): string
    {
        return $this->dateTime->format($format);
    }

    public function inTimezone(string $timezone): self
    {
        return new self($this->dateTime->setTimezone(new DateTimeZone($timezone)));
    }

    public function isPast(): bool
    {
        return $this->dateTime < new DateTimeImmutable();
    }

    public function isFuture(): bool
    {
        return $this->dateTime > new DateTimeImmutable();
    }

    public function diffFrom(self $other): Duration
    {
        $diff = abs($this->unix() - $other->unix());

        return Duration::fromSeconds($diff);
    }

    public function equals(self $other): bool
    {
        return $this->unix() === $other->unix();
    }

    public function isBefore(self $other): bool
    {
        return $this->dateTime < $other->dateTime;
    }

    public function isAfter(self $other): bool
    {
        return $this->dateTime > $other->dateTime;
    }

    public function toArray(): array
    {
        return [
            'unix'    => $this->unix(),
            'iso8601' => $this->iso8601(),
        ];
    }
}
