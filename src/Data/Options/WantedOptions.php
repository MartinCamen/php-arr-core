<?php

namespace MartinCamen\ArrCore\Data\Options;

final readonly class WantedOptions implements RequestOptions
{
    use BuildsRequestParams;

    public function __construct(
        public ?bool $monitored = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [];

        $this->addIfNotNull($params, 'monitored', $this->monitored);

        return $params;
    }

    public static function make(?bool $monitored = null): self
    {
        return new self(monitored: $monitored);
    }

    public function withMonitored(bool $monitored): self
    {
        return new self($monitored);
    }

    public function onlyMonitored(): self
    {
        return new self(true);
    }

    public function onlyUnmonitored(): self
    {
        return new self(false);
    }
}
