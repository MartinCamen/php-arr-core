<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Domain\System;

use MartinCamen\ArrCore\Domain\System\HealthIssue;
use MartinCamen\ArrCore\Domain\System\SystemStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\Timestamp;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SystemStatusTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $status = new SystemStatus(
            source: Service::Sonarr,
            version: '4.0.0.123',
            isHealthy: true,
        );

        $this->assertSame(Service::Sonarr, $status->source);
        $this->assertSame('4.0.0.123', $status->version);
        $this->assertTrue($status->isHealthy);
    }

    #[Test]
    public function detectsHealthIssues(): void
    {
        $healthy = new SystemStatus(
            source: Service::Sonarr,
            version: '4.0.0.123',
            isHealthy: true,
            healthIssues: [],
        );

        $issue = new HealthIssue(
            type: 'warning',
            message: 'Root folder missing',
        );

        $unhealthy = new SystemStatus(
            source: Service::Sonarr,
            version: '4.0.0.123',
            isHealthy: false,
            healthIssues: [$issue],
        );

        $this->assertFalse($healthy->hasIssues());
        $this->assertSame(0, $healthy->issueCount());
        $this->assertTrue($unhealthy->hasIssues());
        $this->assertSame(1, $unhealthy->issueCount());
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $status = SystemStatus::fromArray([
            'source'          => 'radarr',
            'version'         => '5.0.0.456',
            'is_healthy'      => true,
            'start_time'      => '2024-01-01T00:00:00Z',
            'branch'          => 'main',
            'runtime_version' => '8.0.0',
            'os_name'         => 'Linux',
            'health_issues'   => [
                [
                    'type'     => 'warning',
                    'message'  => 'Download client unavailable',
                    'source'   => 'DownloadClientCheck',
                    'wiki_url' => 'https://wiki.servarr.com/radarr',
                ],
            ],
        ]);

        $this->assertSame(Service::Radarr, $status->source);
        $this->assertSame('5.0.0.456', $status->version);
        $this->assertTrue($status->isHealthy);
        $this->assertSame('main', $status->branch);
        $this->assertSame('8.0.0', $status->runtimeVersion);
        $this->assertSame('Linux', $status->osName);
        $this->assertInstanceOf(Timestamp::class, $status->startTime);
        $this->assertCount(1, $status->healthIssues);
        $this->assertInstanceOf(HealthIssue::class, $status->healthIssues[0]);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $status = new SystemStatus(
            source: Service::Sonarr,
            version: '4.0.0.123',
            isHealthy: true,
            branch: 'develop',
            runtimeVersion: '8.0.1',
            osName: 'Windows',
            healthIssues: [
                new HealthIssue(
                    type: 'error',
                    message: 'Indexer unavailable',
                    source: 'IndexerCheck',
                    wikiUrl: 'https://wiki.servarr.com/sonarr',
                ),
            ],
        );

        $array = $status->toArray();

        $this->assertSame('sonarr', $array['source']);
        $this->assertSame('4.0.0.123', $array['version']);
        $this->assertTrue($array['is_healthy']);
        $this->assertSame('develop', $array['branch']);
        $this->assertSame('8.0.1', $array['runtime_version']);
        $this->assertSame('Windows', $array['os_name']);
        $this->assertCount(1, $array['health_issues']);
        $this->assertSame('error', $array['health_issues'][0]['type']);
    }

    #[Test]
    public function handlesMultipleHealthIssues(): void
    {
        $status = new SystemStatus(
            source: Service::Radarr,
            version: '5.0.0.456',
            isHealthy: false,
            healthIssues: [
                new HealthIssue(type: 'warning', message: 'Issue 1'),
                new HealthIssue(type: 'error', message: 'Issue 2'),
                new HealthIssue(type: 'warning', message: 'Issue 3'),
            ],
        );

        $this->assertTrue($status->hasIssues());
        $this->assertSame(3, $status->issueCount());
    }
}
