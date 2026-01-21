<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Options\Concerns;

/**
 * Trait for options classes that support pagination.
 *
 * Provides standard pagination parameters (page/pageSize) used by
 * Radarr, Sonarr, and similar APIs.
 *
 * Usage in an options class:
 *
 *     final readonly class MyOptions implements RequestOptions
 *     {
 *         use HasPagination;
 *
 *         public function __construct(
 *             public ?int $page = null,
 *             public ?int $pageSize = null,
 *             // ... other options
 *         ) {}
 *     }
 */
trait HasPagination
{
    /**
     * Set the page number.
     */
    abstract public function withPage(int $page): static;

    /**
     * Set the page size.
     */
    abstract public function withPageSize(int $pageSize): static;

    /**
     * Add pagination parameters to the array if set.
     *
     * @param array<string, mixed> $params
     */
    protected function addPaginationParams(array &$params): void
    {
        if (property_exists($this, 'page') && $this->page !== null) {
            $params['page'] = $this->page;
        }

        if (property_exists($this, 'pageSize') && $this->pageSize !== null) {
            $params['pageSize'] = $this->pageSize;
        }
    }
}
