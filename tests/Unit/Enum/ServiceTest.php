<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Enum;

use MartinCamen\ArrCore\Enum\Service;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ServiceTest extends TestCase
{
    #[Test]
    public function hasExpectedCases(): void
    {
        $this->assertSame('sonarr', Service::Sonarr->value);
        $this->assertSame('radarr', Service::Radarr->value);
        $this->assertSame('nzbget', Service::NZBGet->value);
        $this->assertSame('jellyseerr', Service::Jellyseerr->value);
    }

    #[Test]
    #[DataProvider('mediaManagerProvider')]
    public function identifiesMediaManagers(Service $service, bool $expected): void
    {
        $this->assertSame($expected, $service->isMediaManager());
    }

    /**
     * @return array<string, array{Service, bool}>
     */
    public static function mediaManagerProvider(): array
    {
        return [
            'Sonarr is media manager'         => [Service::Sonarr, true],
            'Radarr is media manager'         => [Service::Radarr, true],
            'NZBGet is not media manager'     => [Service::NZBGet, false],
            'Jellyseerr is not media manager' => [Service::Jellyseerr, false],
        ];
    }

    #[Test]
    #[DataProvider('downloadClientProvider')]
    public function identifiesDownloadClients(Service $service, bool $expected): void
    {
        $this->assertSame($expected, $service->isDownloadClient());
    }

    /**
     * @return array<string, array{Service, bool}>
     */
    public static function downloadClientProvider(): array
    {
        return [
            'NZBGet is download client'       => [Service::NZBGet, true],
            'SABnzbd is download client'      => [Service::SABnzbd, true],
            'Transmission is download client' => [Service::Transmission, true],
            'Sonarr is not download client'   => [Service::Sonarr, false],
        ];
    }

    #[Test]
    public function identifiesRequestManagers(): void
    {
        $this->assertTrue(Service::Jellyseerr->isRequestManager());
        $this->assertTrue(Service::Overseerr->isRequestManager());
        $this->assertFalse(Service::Sonarr->isRequestManager());
    }

    #[Test]
    public function identifiesIndexerManager(): void
    {
        $this->assertTrue(Service::Prowlarr->isIndexerManager());
        $this->assertFalse(Service::Sonarr->isIndexerManager());
    }

    #[Test]
    public function providesLabels(): void
    {
        $this->assertSame('Sonarr', Service::Sonarr->label());
        $this->assertSame('NZBGet', Service::NZBGet->label());
        $this->assertSame('qBittorrent', Service::QBittorrent->label());
    }
}
