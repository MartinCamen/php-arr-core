<?php

namespace MartinCamen\ArrCore\Data\Responses;

use MartinCamen\ArrCore\Contract\DownloadContract;

abstract class DownloadResponse
{
    /** @param array<string, mixed> $data */
    abstract public static function fromArray(array $data): DownloadContract;

    /** @return array<string, mixed> */
    abstract public function toArray(): array;
}
