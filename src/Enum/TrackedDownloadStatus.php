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
        return match (self::make($status)) {
            self::Ok      => DownloadStatus::Downloading,
            self::Warning => DownloadStatus::Warning,
            self::Error   => DownloadStatus::Failed,
            default       => DownloadStatus::Unknown,
        };
    }

    public static function hasError(string $status): bool
    {
        return match (self::make($status)) {
            self::Warning, self::Error => true,
            default                    => false,
        };
    }

    public static function make(string $status): ?self
    {
        return self::tryFrom(strtolower($status));
    }
}
