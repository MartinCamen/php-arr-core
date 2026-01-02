<?php

namespace MartinCamen\ArrCore\Data\Options;

final readonly class PaginationOptions implements RequestOptions
{
    use BuildsRequestParams;

    public function __construct(
        public int $page = 1,
        public int $pageSize = 10,
    ) {}

    /** @return array{page: int, pageSize: int} */
    public function toArray(): array
    {
        return [
            'page'     => $this->page,
            'pageSize' => $this->pageSize,
        ];
    }

    public static function default(): self
    {
        return new self();
    }

    public function withPage(int $page): self
    {
        return new self($page, $this->pageSize);
    }

    public function withPageSize(int $pageSize): self
    {
        return new self($this->page, $pageSize);
    }
}
