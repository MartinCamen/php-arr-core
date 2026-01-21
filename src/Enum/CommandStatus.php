<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum CommandStatus: string
{
    case Unknown = 'unknown';
    case Queued = 'queued';
    case Started = 'started';
    case Completed = 'completed';
    case Failed = 'failed';
}
