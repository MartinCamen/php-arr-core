<?php

namespace MartinCamen\ArrCore\Data\Options;

use MartinCamen\ArrCore\Data\Enums\SortDirection;

final readonly class SortOptions implements RequestOptions
{
    use BuildsRequestParams;

    public function __construct(
        public ?string $sortKey = null,
        public ?SortDirection $sortDirection = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [];

        $this->addIfNotNull($params, 'sortKey', $this->sortKey);
        $this->addEnumIfNotNull($params, 'sortDirection', $this->sortDirection);

        return $params;
    }

    public static function none(): self
    {
        return new self();
    }

    public static function by(string $key, ?SortDirection $direction = null): self
    {
        return new self($key, $direction);
    }

    public function ascending(): self
    {
        return new self($this->sortKey, SortDirection::Ascending);
    }

    public function descending(): self
    {
        return new self($this->sortKey, SortDirection::Descending);
    }
}
