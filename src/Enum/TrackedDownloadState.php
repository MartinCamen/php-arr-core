<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum TrackedDownloadState: string
{
    case ImportPending = 'importPending';
    case Imported = 'imported';
    case Unknown = 'unknown';

    public static function isCompleted(string $value): bool
    {
        $trackedDownloadState = self::tryFrom($value);

        return $trackedDownloadState === self::ImportPending
            || $trackedDownloadState === self::Imported;
    }
}
