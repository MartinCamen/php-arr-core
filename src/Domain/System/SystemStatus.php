<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\System;

use MartinCamen\ArrCore\Contract\Arrayable;
use MartinCamen\ArrCore\Contract\FromArray;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\Timestamp;

final readonly class SystemStatus implements Arrayable, FromArray
{
    /**
     * @param array<int, HealthIssue> $healthIssues
     */
    public function __construct(
        public Service $source,
        public string $version,
        public bool $isHealthy,
        public ?Timestamp $startTime = null,
        public ?string $branch = null,
        public ?string $runtimeVersion = null,
        public ?string $osName = null,
        public array $healthIssues = [],
    ) {}

    /**
     * Check if service has any health issues.
     */
    public function hasIssues(): bool
    {
        return count($this->healthIssues) > 0;
    }

    /**
     * Get count of health issues.
     */
    public function issueCount(): int
    {
        return count($this->healthIssues);
    }

    /**
     * Get uptime if start time is available.
     */
    public function uptime(): ?string
    {
        if (! $this->startTime instanceof Timestamp) {
            return null;
        }

        $diff = $this->startTime->diffFrom(Timestamp::now());

        return $diff->formatted();
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        /** @var array<int, array<string, mixed>> $rawIssues */
        $rawIssues = $data['health_issues'] ?? [];
        $healthIssues = array_map(
            HealthIssue::fromArray(...),
            $rawIssues,
        );

        return new self(
            source: Service::from((string) $data['source']),
            version: (string) $data['version'],
            isHealthy: (bool) ($data['is_healthy'] ?? true),
            startTime: isset($data['start_time']) ? Timestamp::fromString((string) $data['start_time']) : null,
            branch: isset($data['branch']) ? (string) $data['branch'] : null,
            runtimeVersion: isset($data['runtime_version']) ? (string) $data['runtime_version'] : null,
            osName: isset($data['os_name']) ? (string) $data['os_name'] : null,
            healthIssues: $healthIssues,
        );
    }

    public function toArray(): array
    {
        return [
            'source'          => $this->source->value,
            'version'         => $this->version,
            'is_healthy'      => $this->isHealthy,
            'start_time'      => $this->startTime?->toArray(),
            'branch'          => $this->branch,
            'runtime_version' => $this->runtimeVersion,
            'os_name'         => $this->osName,
            'health_issues'   => array_map(
                fn(HealthIssue $issue): array => $issue->toArray(),
                $this->healthIssues,
            ),
        ];
    }
}
