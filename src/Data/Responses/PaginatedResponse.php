<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

/**
 * Base class for paginated API responses.
 *
 * This is a shared data structure used by both Radarr and Sonarr.
 */
abstract class PaginatedResponse
{
    public function __construct(
        public readonly int $page,
        public readonly int $pageSize,
        public readonly int $totalRecords,
    ) {}

    public function totalPages(): int
    {
        if ($this->pageSize === 0) {
            return 0;
        }

        return (int) ceil($this->totalRecords / $this->pageSize);
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->totalPages();
    }

    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }
}
