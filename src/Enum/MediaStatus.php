<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum MediaStatus: string
{
    /**
     * Status is unknown or unmapped.
     */
    case Unknown = 'unknown';

    /**
     * Media has been announced but not yet available.
     */
    case Announced = 'announced';

    /**
     * Media has been requested (via Jellyseerr/Overseerr).
     */
    case Requested = 'requested';

    /**
     * Media is monitored but file is missing.
     */
    case Missing = 'missing';

    /**
     * Media has been queued for download.
     */
    case Queued = 'queued';

    /**
     * Media is currently downloading.
     */
    case Downloading = 'downloading';

    /**
     * Media has been downloaded and imported.
     */
    case Downloaded = 'downloaded';

    /**
     * Media is fully available (downloaded + meets quality requirements).
     */
    case Available = 'available';

    /**
     * Download or import failed.
     */
    case Failed = 'failed';

    /**
     * Check if status represents an active process.
     */
    public function isActive(): bool
    {
        return match ($this) {
            self::Queued, self::Downloading => true,
            default => false,
        };
    }

    /**
     * Check if status is a terminal state.
     */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::Available, self::Downloaded, self::Failed => true,
            default => false,
        };
    }

    /**
     * Check if status indicates the media needs attention.
     */
    public function needsAttention(): bool
    {
        return match ($this) {
            self::Missing, self::Failed, self::Unknown => true,
            default => false,
        };
    }

    /**
     * Check if status indicates media is obtainable.
     */
    public function hasMedia(): bool
    {
        return match ($this) {
            self::Downloaded, self::Available => true,
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
            self::Downloading => 2,
            self::Queued      => 3,
            self::Missing     => 4,
            self::Requested   => 5,
            self::Announced   => 6,
            self::Downloaded  => 7,
            self::Available   => 8,
            self::Unknown     => 9,
        };
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Unknown     => 'Unknown',
            self::Announced   => 'Announced',
            self::Requested   => 'Requested',
            self::Missing     => 'Missing',
            self::Queued      => 'Queued',
            self::Downloading => 'Downloading',
            self::Downloaded  => 'Downloaded',
            self::Available   => 'Available',
            self::Failed      => 'Failed',
        };
    }

    /**
     * Get a CSS-friendly color class.
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::Unknown                      => 'gray',
            self::Announced, self::Downloading => 'blue',
            self::Requested                    => 'purple',
            self::Missing                      => 'yellow',
            self::Queued                       => 'cyan',
            self::Downloaded, self::Available  => 'green',
            self::Failed                       => 'red',
        };
    }
}
