<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Options\Concerns;

/**
 * Trait for options classes that support filtering.
 *
 * Provides standard filter parameter used by Jellyseerr and similar APIs.
 *
 * Usage in an options class:
 *
 *     final readonly class MyOptions implements RequestOptions
 *     {
 *         use HasFiltering;
 *
 *         public function __construct(
 *             public ?string $filter = null,
 *             // ... other options
 *         ) {}
 *     }
 */
trait HasFiltering
{
    /**
     * Set the filter value.
     */
    abstract public function withFilter(string $filter): static;

    /**
     * Add filter parameter to the array if set.
     *
     * @param array<string, mixed> $params
     */
    protected function addFilterParam(array &$params): void
    {
        if (property_exists($this, 'filter') && $this->filter !== null) {
            $params['filter'] = $this->filter;
        }
    }
}
