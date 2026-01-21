<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum TrackedDownloadStatus: string
{
    case Ok = 'ok';
    case Warning = 'warning';
    case Error = 'error';
    case Unknown = 'unknown';

    public static function fromSonarrToDownloadStatus(string $status): DownloadStatus
    {
        return match (self::tryFrom(strtolower($status))) {
            self::Ok      => DownloadStatus::Downloading,
            self::Warning => DownloadStatus::Warning,
            self::Error   => DownloadStatus::Failed,
            default       => DownloadStatus::Unknown,
        };
    }
}
