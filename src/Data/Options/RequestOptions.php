<?php

namespace MartinCamen\ArrCore\Data\Options;

interface RequestOptions
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
