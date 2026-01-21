<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

/**
 * Jellyseerr request status codes.
 *
 * These are the status values returned by the Jellyseerr/Overseerr API
 * for media requests.
 */
enum JellyseerrRequestStatus: int
{
    case Pending = 1;
    case Approved = 2;
    case Declined = 3;
    case Available = 4;
    case PartiallyAvailable = 5;

    /**
     * Convert to core RequestStatus.
     */
    public function toRequestStatus(): RequestStatus
    {
        return match ($this) {
            self::Pending            => RequestStatus::Pending,
            self::Approved           => RequestStatus::Approved,
            self::Declined           => RequestStatus::Rejected,
            self::Available          => RequestStatus::Fulfilled,
            self::PartiallyAvailable => RequestStatus::Approved,
        };
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending            => 'Pending',
            self::Approved           => 'Approved',
            self::Declined           => 'Declined',
            self::Available          => 'Available',
            self::PartiallyAvailable => 'Partially Available',
        };
    }

    /**
     * Check if request is pending review.
     */
    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Check if request has been approved.
     */
    public function isApproved(): bool
    {
        return match ($this) {
            self::Approved, self::Available, self::PartiallyAvailable => true,
            default                                                   => false,
        };
    }

    /**
     * Check if request was declined.
     */
    public function isDeclined(): bool
    {
        return $this === self::Declined;
    }

    /**
     * Check if request is fulfilled (media available).
     */
    public function isFulfilled(): bool
    {
        return $this === self::Available;
    }

    /**
     * Check if request needs action.
     */
    public function needsAction(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Check if request is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::Declined, self::Available => true,
            default                         => false,
        };
    }

    /**
     * Get a CSS-friendly color class.
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::Pending            => 'yellow',
            self::Approved           => 'blue',
            self::Declined           => 'red',
            self::Available          => 'green',
            self::PartiallyAvailable => 'cyan',
        };
    }
}
