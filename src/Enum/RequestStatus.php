<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum RequestStatus: string
{
    /**
     * Request is pending approval.
     */
    case Pending = 'pending';

    /**
     * Request has been approved.
     */
    case Approved = 'approved';

    /**
     * Request has been rejected/declined.
     */
    case Rejected = 'rejected';

    /**
     * Request has been fulfilled (media available).
     */
    case Fulfilled = 'fulfilled';

    /**
     * Request failed to be fulfilled.
     */
    case Failed = 'failed';

    /**
     * Check if request is pending review.
     */
    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Check if request is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::Rejected, self::Fulfilled, self::Failed => true,
            default => false,
        };
    }

    /**
     * Check if request was successful.
     */
    public function isSuccessful(): bool
    {
        return match ($this) {
            self::Approved, self::Fulfilled => true,
            default => false,
        };
    }

    /**
     * Check if request needs action.
     */
    public function needsAction(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Pending',
            self::Approved  => 'Approved',
            self::Rejected  => 'Rejected',
            self::Fulfilled => 'Fulfilled',
            self::Failed    => 'Failed',
        };
    }

    /**
     * Get a CSS-friendly color class.
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::Pending   => 'yellow',
            self::Approved  => 'blue',
            self::Rejected  => 'red',
            self::Fulfilled => 'green',
            self::Failed    => 'red',
        };
    }
}
