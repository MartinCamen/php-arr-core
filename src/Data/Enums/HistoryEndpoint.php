<?php

namespace MartinCamen\ArrCore\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum HistoryEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'history';
    case Movie = 'history/movie';
    case Since = 'history/since';
    case Series = 'history/series';
    case Failed = 'history/failed/{id}';

    public function defaultResponse(): mixed
    {
        return match ($this) {
            self::All                              => [
                'page'         => 1,
                'pageSize'     => 10,
                'totalRecords' => 0,
                'records'      => [],
            ],
            self::Since, self::Movie, self::Series => [],
            self::Failed                           => null,
        };
    }
}
