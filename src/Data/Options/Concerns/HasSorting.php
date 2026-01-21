<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Options\Concerns;

use MartinCamen\ArrCore\Data\Enums\SortDirection;

/**
 * Trait for options classes that support sorting.
 *
 * Provides standard sorting parameters (sortKey/sortDirection) used by
 * Radarr, Sonarr, and similar APIs.
 *
 * Usage in an options class:
 *
 *     final readonly class MyOptions implements RequestOptions
 *     {
 *         use HasSorting;
 *
 *         public function __construct(
 *             public ?string $sortKey = null,
 *             public ?SortDirection $sortDirection = null,
 *             // ... other options
 *         ) {}
 *     }
 */
trait HasSorting
{
    /**
     * Set the sort key.
     */
    abstract public function withSortKey(string $sortKey): static;

    /**
     * Set the sort direction.
     */
    abstract public function withSortDirection(SortDirection $direction): static;

    /**
     * Sort in ascending order.
     */
    public function ascending(): static
    {
        return $this->withSortDirection(SortDirection::Ascending);
    }

    /**
     * Sort in descending order.
     */
    public function descending(): static
    {
        return $this->withSortDirection(SortDirection::Descending);
    }

    /**
     * Add sorting parameters to the array if set.
     *
     * @param array<string, mixed> $params
     */
    protected function addSortingParams(array &$params): void
    {
        if (property_exists($this, 'sortKey') && $this->sortKey !== null) {
            $params['sortKey'] = $this->sortKey;
        }

        if (property_exists($this, 'sortDirection') && $this->sortDirection !== null) {
            $params['sortDirection'] = $this->sortDirection->value;
        }
    }
}
