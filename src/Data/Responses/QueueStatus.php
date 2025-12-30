<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

/**
 * Represents queue status from *arr APIs.
 *
 * This is a shared data structure used by both Radarr and Sonarr.
 */
final readonly class QueueStatus
{
    public function __construct(
        public int $totalCount,
        public int $count,
        public int $unknownCount,
        public bool $errors,
        public bool $warnings,
        public bool $unknownErrors,
        public bool $unknownWarnings,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            totalCount: $data['totalCount'] ?? 0,
            count: $data['count'] ?? 0,
            unknownCount: $data['unknownCount'] ?? 0,
            errors: $data['errors'] ?? false,
            warnings: $data['warnings'] ?? false,
            unknownErrors: $data['unknownErrors'] ?? false,
            unknownWarnings: $data['unknownWarnings'] ?? false,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'total_count'      => $this->totalCount,
            'count'            => $this->count,
            'unknown_count'    => $this->unknownCount,
            'errors'           => $this->errors,
            'warnings'         => $this->warnings,
            'unknown_errors'   => $this->unknownErrors,
            'unknown_warnings' => $this->unknownWarnings,
        ];
    }

    public function hasErrors(): bool
    {
        return $this->errors || $this->unknownErrors;
    }

    public function hasWarnings(): bool
    {
        return $this->warnings || $this->unknownWarnings;
    }

    public function hasIssues(): bool
    {
        return $this->hasErrors() || $this->hasWarnings();
    }

    public function isEmpty(): bool
    {
        return $this->totalCount === 0;
    }
}
