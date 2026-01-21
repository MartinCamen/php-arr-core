<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

use ArrayIterator;
use MartinCamen\ArrCore\Contract\PaginatedResponse as PaginatedResponseInterface;
use Traversable;

/**
 * Base class for paginated API responses.
 *
 * This is a shared data structure used by Radarr, Sonarr, Jellyseerr, etc.
 *
 * @template T
 *
 * @implements PaginatedResponseInterface<T>
 */
abstract class PaginatedResponse implements PaginatedResponseInterface
{
    /**
     * @param int $currentPage The current page number (1-indexed)
     * @param int $itemsPerPage The number of items per page
     * @param int $totalItems The total number of items across all pages
     */
    public function __construct(
        protected readonly int $currentPage,
        protected readonly int $itemsPerPage,
        protected readonly int $totalItems,
    ) {}

    /**
     * Get all items on the current page.
     *
     * @return array<int, T>
     */
    abstract public function all(): array;

    public function total(): int
    {
        return $this->totalItems;
    }

    public function page(): int
    {
        return $this->currentPage;
    }

    public function pages(): int
    {
        if ($this->itemsPerPage === 0) {
            return 0;
        }

        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }

    public function pageSize(): int
    {
        return $this->itemsPerPage;
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->pages();
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }
}
