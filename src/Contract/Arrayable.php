<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Objects implementing this interface can be converted to an array representation.
 */
interface Arrayable
{
    /**
     * Convert the object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
