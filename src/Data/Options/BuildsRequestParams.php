<?php

namespace MartinCamen\ArrCore\Data\Options;

use BackedEnum;
use DateTimeInterface;

trait BuildsRequestParams
{
    /**
     * Add a parameter to the array if the value is not null.
     *
     * @param array<string, mixed> $params
     */
    protected function addIfNotNull(array &$params, string $key, mixed $value): void
    {
        if ($value !== null) {
            $params[$key] = $value;
        }
    }

    /**
     * Add a date parameter formatted as Y-m-d if the value is not null.
     *
     * @param array<string, mixed> $params
     */
    protected function addDateIfNotNull(array &$params, string $key, ?DateTimeInterface $value): void
    {
        if ($value instanceof DateTimeInterface) {
            $params[$key] = $value->format('Y-m-d');
        }
    }

    /**
     * Add an array parameter as a comma-separated string if not null.
     *
     * @param array<int, int>|null $value
     * @param array<string, mixed> $params
     */
    protected function addArrayAsStringIfNotNull(array &$params, string $key, ?array $value): void
    {
        if ($value !== null) {
            $params[$key] = implode(',', $value);
        }
    }

    /**
     * Add an enum value parameter if not null.
     *
     * @param array<string, mixed> $params
     */
    protected function addEnumIfNotNull(array &$params, string $key, mixed $value): void
    {
        if ($value instanceof BackedEnum) {
            $params[$key] = $value->value;
        }
    }
}
