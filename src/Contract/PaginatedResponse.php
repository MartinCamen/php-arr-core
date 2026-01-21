<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

use Countable;
use IteratorAggregate;

/**
 * Interface for paginated API responses across all *arr services.
 *
 * Implementing this interface ensures consistent pagination handling
 * regardless of which service (Radarr, Sonarr, Jellyseerr, etc.) the
 * response came from.
 *
 * @template T
 *
 * @extends IteratorAggregate<int, T>
 */
interface PaginatedResponse extends Countable, IteratorAggregate
{
    /**
     * Get all items on the current page.
     *
     * @return array<int, T>
     */
    public function all(): array;

    /**
     * Get the total number of items across all pages.
     */
    public function total(): int;

    /**
     * Get the current page number (1-indexed).
     */
    public function page(): int;

    /**
     * Get the total number of pages.
     */
    public function pages(): int;

    /**
     * Get the number of items per page.
     */
    public function pageSize(): int;

    /**
     * Check if the current page has no items.
     */
    public function isEmpty(): bool;
}
