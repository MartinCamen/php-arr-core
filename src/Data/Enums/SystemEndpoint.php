<?php

namespace MartinCamen\ArrCore\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum SystemEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case Status = 'system/status';
    case Health = 'health';
    case DiskSpace = 'diskspace';
    case Task = 'system/task';
    case TaskById = 'system/task/{id}';
    case Backup = 'system/backup';

    /** @return array<int, mixed> */
    public function defaultResponse(): array
    {
        return match ($this) {
            self::Status,
            self::Task,
            self::DiskSpace,
            self::Backup,
            self::TaskById,
            self::Health => [],
        };
    }
}
