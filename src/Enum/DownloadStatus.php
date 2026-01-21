<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum DownloadStatus: string
{
    case Unknown = 'unknown';
    case Queued = 'queued';
    case Paused = 'paused';
    case Downloading = 'downloading';
    case Verifying = 'verifying';
    case Extracting = 'extracting';
    case Importing = 'importing';
    case Completed = 'completed';
    case Warning = 'warning';
    case Failed = 'failed';

    public function isActive(): bool
    {
        return match ($this) {
            self::Downloading, self::Verifying, self::Extracting, self::Importing => true,
            default => false,
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Completed, self::Failed => true,
            default => false,
        };
    }

    public function isError(): bool
    {
        return match ($this) {
            self::Failed, self::Warning => true,
            default => false,
        };
    }

    public function isWaiting(): bool
    {
        return match ($this) {
            self::Queued, self::Paused => true,
            default => false,
        };
    }

    public function isPostProcessing(): bool
    {
        return match ($this) {
            self::Verifying, self::Extracting, self::Importing => true,
            default => false,
        };
    }

    /** Get status priority for sorting (lower = more urgent) */
    public function priority(): int
    {
        return match ($this) {
            self::Failed      => 1,
            self::Warning     => 2,
            self::Downloading => 3,
            self::Verifying   => 4,
            self::Extracting  => 5,
            self::Importing   => 6,
            self::Queued      => 7,
            self::Paused      => 8,
            self::Completed   => 9,
            self::Unknown     => 10,
        };
    }

    /** Get human-readable label */
    public function label(): string
    {
        return match ($this) {
            self::Unknown     => 'Unknown',
            self::Queued      => 'Queued',
            self::Paused      => 'Paused',
            self::Downloading => 'Downloading',
            self::Verifying   => 'Verifying',
            self::Extracting  => 'Extracting',
            self::Importing   => 'Importing',
            self::Completed   => 'Completed',
            self::Warning     => 'Warning',
            self::Failed      => 'Failed',
        };
    }

    /** Get a CSS-friendly color class */
    public function colorClass(): string
    {
        return match ($this) {
            self::Unknown, self::Paused                        => 'gray',
            self::Queued                                       => 'cyan',
            self::Downloading                                  => 'blue',
            self::Verifying, self::Importing, self::Extracting => 'purple',
            self::Completed                                    => 'green',
            self::Warning                                      => 'yellow',
            self::Failed                                       => 'red',
        };
    }
}
