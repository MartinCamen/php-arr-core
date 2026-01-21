<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

use ArrayIterator;
use MartinCamen\ArrCore\Contract\PaginatedResponse as PaginatedResponseInterface;
use MartinCamen\ArrCore\Data\Responses\Concerns\HasPaginatedResponse;
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
    use HasPaginatedResponse;

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

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }
}
