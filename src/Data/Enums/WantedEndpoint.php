<?php

namespace MartinCamen\ArrCore\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum WantedEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case Missing = 'wanted/missing';
    case Cutoff = 'wanted/cutoff';

    /** @return array<string, int|array<int|mixed>> */
    public function defaultResponse(): array
    {
        return [
            'page'         => 1,
            'pageSize'     => 10,
            'totalRecords' => 0,
            'records'      => [],
        ];
    }
}
