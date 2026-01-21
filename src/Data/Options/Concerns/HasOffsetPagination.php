<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Options\Concerns;

/**
 * Trait for options classes that support offset-based pagination.
 *
 * Provides take/skip parameters used by Jellyseerr and similar APIs.
 *
 * Usage in an options class:
 *
 *     final readonly class MyOptions implements RequestOptions
 *     {
 *         use HasOffsetPagination;
 *
 *         public function __construct(
 *             public ?int $take = null,
 *             public ?int $skip = null,
 *             // ... other options
 *         ) {}
 *     }
 */
trait HasOffsetPagination
{
    /**
     * Set the number of items to take.
     */
    abstract public function withTake(int $take): static;

    /**
     * Set the number of items to skip.
     */
    abstract public function withSkip(int $skip): static;

    /**
     * Add offset pagination parameters to the array if set.
     *
     * @param array<string, mixed> $params
     */
    protected function addOffsetPaginationParams(array &$params): void
    {
        if (property_exists($this, 'take') && $this->take !== null) {
            $params['take'] = $this->take;
        }

        if (property_exists($this, 'skip') && $this->skip !== null) {
            $params['skip'] = $this->skip;
        }
    }
}
