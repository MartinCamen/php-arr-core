<?php

namespace MartinCamen\ArrCore\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum CalendarEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'calendar';
    case ById = 'calendar/{id}';

    /** @return array<string, mixed> */
    public function defaultResponse(): array
    {
        return match ($this) {
            self::All, self::ById => [],
        };
    }
}
