<?php

namespace MartinCamen\ArrCore\Data\Responses;

use MartinCamen\ArrCore\Contract\QueueRecordContract;

abstract class QueueRecordResponse
{
    /** @param array<string, mixed> $data */
    abstract public static function fromArray(array $data): QueueRecordContract;

    /** @return array<string, mixed> */
    abstract public function toArray(): array;
}
