<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Mapping;

use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemStatus;
use MartinCamen\ArrCore\Domain\System\HealthCheck;
use MartinCamen\ArrCore\Domain\System\HealthIssue;
use MartinCamen\ArrCore\Domain\System\SystemStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\Timestamp;

class ServiceToCoreMapper
{
    /**
     * Map Radarr SystemStatus to Core SystemStatus.
     *
     * @param array<int, HealthCheck> $healthChecks
     */
    public static function mapToSystemStatus(
        Service $service,
        DownloadServiceSystemStatus $dto,
        array $healthChecks = [],
    ): SystemStatus {
        $issues = array_map(
            static fn(HealthCheck $check): HealthIssue => new HealthIssue(
                type: $check->type,
                message: $check->message,
                source: $check->source,
                wikiUrl: $check->wikiUrl,
            ),
            $healthChecks,
        );

        return new SystemStatus(
            source: $service,
            version: $dto->version,
            isHealthy: count($issues) === 0,
            startTime: $dto->startTime !== '' ? Timestamp::fromString($dto->startTime) : null,
            branch: $dto->branch,
            runtimeVersion: $dto->runtimeVersion,
            osName: $dto->osName,
            healthIssues: $issues,
        );
    }

    /**
     * Parse a .NET TimeSpan string (d.hh:mm:ss or hh:mm:ss) to Duration.
     */
    protected static function parseTimeSpan(?string $timeSpan): ?Duration
    {
        if ($timeSpan === null || $timeSpan === '') {
            return null;
        }

        $seconds = 0;

        // Handle format: d.hh:mm:ss or hh:mm:ss
        if (str_contains($timeSpan, '.')) {
            [$days, $time] = explode('.', $timeSpan, 2);
            $seconds += (int) $days * 86400;
            $timeSpan = $time;
        }

        $parts = explode(':', $timeSpan);
        if (count($parts) === 3) {
            $seconds += (int) $parts[0] * 3600;
            $seconds += (int) $parts[1] * 60;
            $seconds += (int) $parts[2];
        }

        return Duration::fromSeconds($seconds);
    }

    /**
     * Extract image URL from images array.
     *
     * @param array<int, array<string, mixed>> $images
     */
    protected static function extractImage(array $images, string $type): ?string
    {
        foreach ($images as $image) {
            if (($image['coverType'] ?? '') === $type) {
                return $image['remoteUrl'] ?? $image['url'] ?? null;
            }
        }

        return null;
    }
}
