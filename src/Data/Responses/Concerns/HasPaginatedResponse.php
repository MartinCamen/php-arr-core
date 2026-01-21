<?php

namespace MartinCamen\ArrCore\Data\Responses\Concerns;

/**
 * @property int $totalItems
 * @property int $currentPage
 * @property int $itemsPerPage
 * @method all()
 */
trait HasPaginatedResponse
{
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
}
