<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\System;

use MartinCamen\ArrCore\Contract\Arrayable;

final readonly class HealthIssue implements Arrayable
{
    public function __construct(
        public string $type,
        public string $message,
        public string $source = '',
        public string $wikiUrl = '',
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: (string) ($data['type'] ?? 'unknown'),
            message: (string) ($data['message'] ?? ''),
            source: (string) ($data['source'] ?? ''),
            wikiUrl: (string) ($data['wiki_url'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'type'     => $this->type,
            'message'  => $this->message,
            'source'   => $this->source,
            'wiki_url' => $this->wikiUrl,
        ];
    }
}
