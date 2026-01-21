<?php

namespace MartinCamen\ArrCore\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum QueueEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'queue';
    case ById = 'queue/{id}';
    case Details = 'queue/details';
    case Bulk = 'queue/bulk';
    case Status = 'queue/status';

    /** @return null|array<int|string, int|array<int, mixed>> */
    public function defaultResponse(): ?array
    {
        return match ($this) {
            self::All                   => ['page' => 1, 'pageSize' => 10, 'totalRecords' => 0, 'records' => []],
            self::ById, self::Bulk      => null,
            self::Details, self::Status => [],
        };
    }
}
