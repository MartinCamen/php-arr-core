<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\System;

use MartinCamen\ArrCore\Contract\Arrayable;

final readonly class HealthCheck implements Arrayable
{
    public function __construct(
        public string $source,
        public string $type,
        public string $message,
        public string $wikiUrl,
    ) {}

    /** @param array<string, string|null> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            source: $data['source'] ?? '',
            type: $data['type'] ?? 'unknown',
            message: $data['message'] ?? '',
            wikiUrl: $data['wikiUrl'] ?? '',
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'source'   => $this->source,
            'type'     => $this->type,
            'message'  => $this->message,
            'wiki_url' => $this->wikiUrl,
        ];
    }

    public function isWarning(): bool
    {
        return $this->type === 'warning';
    }

    public function isError(): bool
    {
        return $this->type === 'error';
    }
}
