<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\ValueObject;

use InvalidArgumentException;
use MartinCamen\ArrCore\Contract\Arrayable;
use Stringable;

final readonly class ArrId implements Arrayable, Stringable
{
    private function __construct(private int|string $value)
    {
        if (is_string($this->value) && trim($this->value) === '') {
            throw new InvalidArgumentException('ArrId cannot be an empty string');
        }
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public static function from(int|string $id): self
    {
        return new self($id);
    }

    public function value(): int|string
    {
        return $this->value;
    }

    public function isInt(): bool
    {
        return is_int($this->value);
    }

    public function isString(): bool
    {
        return is_string($this->value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function toArray(): array
    {
        return ['id' => $this->value];
    }
}
