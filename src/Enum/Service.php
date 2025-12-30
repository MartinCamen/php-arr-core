<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum Service: string
{
    case Sonarr = 'sonarr';
    case Radarr = 'radarr';
    case Lidarr = 'lidarr';
    case Readarr = 'readarr';
    case Prowlarr = 'prowlarr';
    case Jellyseerr = 'jellyseerr';
    case Overseerr = 'overseerr';
    case NZBGet = 'nzbget';
    case SABnzbd = 'sabnzbd';
    case Transmission = 'transmission';
    case QBittorrent = 'qbittorrent';
    case Deluge = 'deluge';

    /**
     * Check if service is an *arr media manager.
     */
    public function isMediaManager(): bool
    {
        return match ($this) {
            self::Sonarr, self::Radarr, self::Lidarr, self::Readarr => true,
            default => false,
        };
    }

    /**
     * Check if service is a request manager (Jellyseerr/Overseerr).
     */
    public function isRequestManager(): bool
    {
        return match ($this) {
            self::Jellyseerr, self::Overseerr => true,
            default => false,
        };
    }

    /**
     * Check if service is a download client.
     */
    public function isDownloadClient(): bool
    {
        return match ($this) {
            self::NZBGet, self::SABnzbd, self::Transmission, self::QBittorrent, self::Deluge => true,
            default => false,
        };
    }

    /**
     * Check if service is an indexer manager.
     */
    public function isIndexerManager(): bool
    {
        return $this === self::Prowlarr;
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Sonarr       => 'Sonarr',
            self::Radarr       => 'Radarr',
            self::Lidarr       => 'Lidarr',
            self::Readarr      => 'Readarr',
            self::Prowlarr     => 'Prowlarr',
            self::Jellyseerr   => 'Jellyseerr',
            self::Overseerr    => 'Overseerr',
            self::NZBGet       => 'NZBGet',
            self::SABnzbd      => 'SABnzbd',
            self::Transmission => 'Transmission',
            self::QBittorrent  => 'qBittorrent',
            self::Deluge       => 'Deluge',
        };
    }
}
