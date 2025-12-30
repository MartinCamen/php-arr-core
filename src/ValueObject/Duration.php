<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\ValueObject;

use DateInterval;
use InvalidArgumentException;
use MartinCamen\ArrCore\Contract\Arrayable;

final readonly class Duration implements Arrayable
{
    private const int SECONDS_PER_MINUTE = 60;
    private const int SECONDS_PER_HOUR = 3600;
    private const int SECONDS_PER_DAY = 86400;

    private function __construct(private int $seconds)
    {
        if ($this->seconds < 0) {
            throw new InvalidArgumentException('Duration cannot be negative');
        }
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function fromSeconds(int $seconds): self
    {
        return new self($seconds);
    }

    public static function fromMinutes(float $minutes): self
    {
        return new self((int) round($minutes * self::SECONDS_PER_MINUTE));
    }

    public static function fromHours(float $hours): self
    {
        return new self((int) round($hours * self::SECONDS_PER_HOUR));
    }

    public static function fromDateInterval(DateInterval $interval): self
    {
        $seconds = $interval->s
            + ($interval->i * self::SECONDS_PER_MINUTE)
            + ($interval->h * self::SECONDS_PER_HOUR)
            + ($interval->d * self::SECONDS_PER_DAY);

        return new self($seconds);
    }

    public function seconds(): int
    {
        return $this->seconds;
    }

    public function minutes(): float
    {
        return $this->seconds / self::SECONDS_PER_MINUTE;
    }

    public function hours(): float
    {
        return $this->seconds / self::SECONDS_PER_HOUR;
    }

    public function toDateInterval(): DateInterval
    {
        return new DateInterval('PT' . $this->seconds . 'S');
    }

    public function formatted(): string
    {
        if ($this->seconds === 0) {
            return '0s';
        }

        $parts = [];

        $remaining = $this->seconds;

        $days = (int) floor($remaining / self::SECONDS_PER_DAY);
        $remaining %= self::SECONDS_PER_DAY;

        $hours = (int) floor($remaining / self::SECONDS_PER_HOUR);
        $remaining %= self::SECONDS_PER_HOUR;

        $minutes = (int) floor($remaining / self::SECONDS_PER_MINUTE);
        $seconds = $remaining % self::SECONDS_PER_MINUTE;

        if ($days > 0) {
            $parts[] = $days . 'd';
        }
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($minutes > 0) {
            $parts[] = $minutes . 'm';
        }
        if ($seconds > 0) {
            $parts[] = $seconds . 's';
        }

        return implode(' ', $parts);
    }

    public function isZero(): bool
    {
        return $this->seconds === 0;
    }

    public function add(self $other): self
    {
        return new self($this->seconds + $other->seconds);
    }

    public function subtract(self $other): self
    {
        return new self(max(0, $this->seconds - $other->seconds));
    }

    public function equals(self $other): bool
    {
        return $this->seconds === $other->seconds;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->seconds > $other->seconds;
    }

    public function toArray(): array
    {
        return [
            'seconds'   => $this->seconds,
            'formatted' => $this->formatted(),
        ];
    }
}
