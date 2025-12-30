<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Objects implementing this interface can be constructed from an array.
 */
interface FromArray
{
    /**
     * Create an instance from an array of data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static;
}
