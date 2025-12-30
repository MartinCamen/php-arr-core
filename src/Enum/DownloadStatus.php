<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum DownloadStatus: string
{
    /**
     * Status is unknown or unmapped.
     */
    case Unknown = 'unknown';

    /**
     * Download is queued, waiting to start.
     */
    case Queued = 'queued';

    /**
     * Download is paused.
     */
    case Paused = 'paused';

    /**
     * Download is in progress.
     */
    case Downloading = 'downloading';

    /**
     * Download is being verified (par2 check).
     */
    case Verifying = 'verifying';

    /**
     * Download is being extracted/unpacked.
     */
    case Extracting = 'extracting';

    /**
     * Download is being imported/moved to library.
     */
    case Importing = 'importing';

    /**
     * Download completed successfully.
     */
    case Completed = 'completed';

    /**
     * Download has a warning (stalled, slow, etc).
     */
    case Warning = 'warning';

    /**
     * Download failed.
     */
    case Failed = 'failed';

    /**
     * Check if download is actively progressing.
     */
    public function isActive(): bool
    {
        return match ($this) {
            self::Downloading, self::Verifying, self::Extracting, self::Importing => true,
            default => false,
        };
    }

    /**
     * Check if download is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::Completed, self::Failed => true,
            default => false,
        };
    }

    /**
     * Check if download has an error or warning.
     */
    public function isError(): bool
    {
        return match ($this) {
            self::Failed, self::Warning => true,
            default => false,
        };
    }

    /**
     * Check if download is waiting (not actively processing).
     */
    public function isWaiting(): bool
    {
        return match ($this) {
            self::Queued, self::Paused => true,
            default => false,
        };
    }

    /**
     * Check if download is in post-processing.
     */
    public function isPostProcessing(): bool
    {
        return match ($this) {
            self::Verifying, self::Extracting, self::Importing => true,
            default => false,
        };
    }

    /**
     * Get status priority for sorting (lower = more urgent).
     */
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

    /**
     * Get human-readable label.
     */
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

    /**
     * Get a CSS-friendly color class.
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::Unknown     => 'gray',
            self::Queued      => 'cyan',
            self::Paused      => 'gray',
            self::Downloading => 'blue',
            self::Verifying   => 'purple',
            self::Extracting  => 'purple',
            self::Importing   => 'purple',
            self::Completed   => 'green',
            self::Warning     => 'yellow',
            self::Failed      => 'red',
        };
    }
}
