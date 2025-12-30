<?php

namespace MartinCamen\ArrCore\Domain\System;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, HealthCheck>
 */
final class HealthCheckCollection implements Countable, IteratorAggregate
{
    /** @param array<int, HealthCheck> $checks */
    public function __construct(private array $checks = []) {}

    /** @param array<int, array<string, mixed>> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            array_map(
                HealthCheck::fromArray(...),
                $data,
            ),
        );
    }

    /** @return array<int, HealthCheck> */
    public function all(): array
    {
        return $this->checks;
    }

    public function count(): int
    {
        return count($this->checks);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function first(): ?HealthCheck
    {
        return $this->checks[0] ?? null;
    }

    public function last(): ?HealthCheck
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->checks[$this->count() - 1];
    }

    public function get(int $index): ?HealthCheck
    {
        return $this->checks[$index] ?? null;
    }

    /** @return Traversable<int, HealthCheck> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->checks);
    }

    /** @return array<int, array<string, mixed>> */
    public function toArray(): array
    {
        return array_map(
            fn(HealthCheck $check): array => $check->toArray(),
            $this->checks,
        );
    }

    public function warnings(): self
    {
        return new self(
            array_values(array_filter(
                $this->checks,
                fn(HealthCheck $check): bool => $check->isWarning(),
            )),
        );
    }

    public function errors(): self
    {
        return new self(
            array_values(array_filter(
                $this->checks,
                fn(HealthCheck $check): bool => $check->isError(),
            )),
        );
    }

    public function hasErrors(): bool
    {
        return ! $this->errors()->isEmpty();
    }

    public function hasWarnings(): bool
    {
        return ! $this->warnings()->isEmpty();
    }
}
