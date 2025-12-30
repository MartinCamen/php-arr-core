<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\ValueObject;

use MartinCamen\ArrCore\Contract\Arrayable;

final readonly class Progress implements Arrayable
{
    private function __construct(
        private float $ratio,
    ) {}

    public static function zero(): self
    {
        return new self(0.0);
    }

    public static function complete(): self
    {
        return new self(1.0);
    }

    public static function fromRatio(float $ratio): self
    {
        return new self(self::clamp($ratio, 0.0, 1.0));
    }

    public static function fromPercentage(float $percentage): self
    {
        return new self(self::clamp($percentage / 100.0, 0.0, 1.0));
    }

    public static function fromFraction(int $completed, int $total): self
    {
        if ($total <= 0) {
            return self::zero();
        }

        return new self(self::clamp($completed / $total, 0.0, 1.0));
    }

    public function ratio(): float
    {
        return $this->ratio;
    }

    public function percentage(): float
    {
        return $this->ratio * 100.0;
    }

    public function remaining(): float
    {
        return (1.0 - $this->ratio) * 100.0;
    }

    public function isZero(): bool
    {
        return $this->ratio === 0.0;
    }

    public function isComplete(): bool
    {
        return $this->ratio >= 1.0;
    }

    public function formatted(int $precision = 1): string
    {
        return round($this->percentage(), $precision) . '%';
    }

    public function equals(self $other): bool
    {
        return abs($this->ratio - $other->ratio) < 0.0001;
    }

    public function toArray(): array
    {
        return [
            'ratio'      => $this->ratio,
            'percentage' => $this->percentage(),
            'formatted'  => $this->formatted(),
        ];
    }

    private static function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
