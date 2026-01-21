<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

/**
 * Jellyseerr media status codes.
 *
 * These are the status values returned by the Jellyseerr/Overseerr API
 * for media items (movies/series).
 */
enum JellyseerrMediaStatus: int
{
    case Unknown = 0;
    case Pending = 1;
    case Processing = 2;
    case PartiallyAvailable = 3;
    case Available = 4;
    case Deleted = 5;

    /**
     * Convert to core MediaStatus.
     */
    public function toMediaStatus(): MediaStatus
    {
        return match ($this) {
            self::Unknown            => MediaStatus::Unknown,
            self::Pending            => MediaStatus::Requested,
            self::Processing         => MediaStatus::Queued,
            self::PartiallyAvailable => MediaStatus::Downloading,
            self::Available          => MediaStatus::Available,
            self::Deleted            => MediaStatus::Failed,
        };
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Unknown            => 'Unknown',
            self::Pending            => 'Pending',
            self::Processing         => 'Processing',
            self::PartiallyAvailable => 'Partially Available',
            self::Available          => 'Available',
            self::Deleted            => 'Deleted',
        };
    }

    /**
     * Check if media is available (fully or partially).
     */
    public function isAvailable(): bool
    {
        return match ($this) {
            self::Available, self::PartiallyAvailable => true,
            default                                   => false,
        };
    }

    /**
     * Check if media is in a processing state.
     */
    public function isProcessing(): bool
    {
        return $this === self::Processing;
    }

    /**
     * Check if media is pending/requested.
     */
    public function isPending(): bool
    {
        return $this === self::Pending;
    }
}
