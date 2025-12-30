<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Mapping;

use MartinCamen\ArrCore\Enum\DownloadStatus;
use MartinCamen\ArrCore\Enum\MediaStatus;
use MartinCamen\ArrCore\Enum\RequestStatus;

/**
 * Centralizes all status normalization logic for *arr services.
 *
 * This class is:
 * - Pure (no side effects)
 * - Deterministic (same input = same output)
 * - Stateless (no dependencies or configuration)
 *
 * All service-specific status values are mapped exactly once, here.
 */
final class StatusNormalizer
{
    // =========================================================================
    // Sonarr Mappings
    // =========================================================================

    /**
     * Normalize Sonarr series status to MediaStatus.
     *
     * @param string $status Sonarr series status (continuing, ended, upcoming, deleted)
     */
    public static function mediaFromSonarr(string $status, bool $hasFiles = false): MediaStatus
    {
        return match (strtolower($status)) {
            'continuing' => $hasFiles ? MediaStatus::Available : MediaStatus::Missing,
            'ended'      => $hasFiles ? MediaStatus::Available : MediaStatus::Missing,
            'upcoming'   => MediaStatus::Announced,
            'deleted'    => MediaStatus::Unknown,
            default      => MediaStatus::Unknown,
        };
    }

    /**
     * Normalize Sonarr queue item status to DownloadStatus.
     *
     * @param string $status Sonarr queue status
     * @param string|null $trackedDownloadStatus Additional status info
     */
    public static function downloadFromSonarrQueue(
        string $status,
        ?string $trackedDownloadStatus = null,
    ): DownloadStatus {
        // Handle tracked download status first if available
        if ($trackedDownloadStatus !== null) {
            $mapped = self::downloadFromSonarrTrackedStatus($trackedDownloadStatus);
            if ($mapped !== DownloadStatus::Unknown) {
                return $mapped;
            }
        }

        return match (strtolower($status)) {
            'queued'                    => DownloadStatus::Queued,
            'paused'                    => DownloadStatus::Paused,
            'downloading'               => DownloadStatus::Downloading,
            'completed'                 => DownloadStatus::Completed,
            'failed'                    => DownloadStatus::Failed,
            'warning'                   => DownloadStatus::Warning,
            'delay'                     => DownloadStatus::Queued,
            'downloadclientunavailable' => DownloadStatus::Warning,
            default                     => DownloadStatus::Unknown,
        };
    }

    /**
     * Normalize Sonarr tracked download status.
     */
    private static function downloadFromSonarrTrackedStatus(string $status): DownloadStatus
    {
        return match (strtolower($status)) {
            'ok'      => DownloadStatus::Downloading,
            'warning' => DownloadStatus::Warning,
            'error'   => DownloadStatus::Failed,
            default   => DownloadStatus::Unknown,
        };
    }

    // =========================================================================
    // Radarr Mappings
    // =========================================================================

    /**
     * Normalize Radarr movie status to MediaStatus.
     *
     * @param string $status Radarr movie status (tba, announced, inCinemas, released, deleted)
     */
    public static function mediaFromRadarr(string $status, bool $hasFile = false): MediaStatus
    {
        return match (strtolower($status)) {
            'released'  => $hasFile ? MediaStatus::Available : MediaStatus::Missing,
            'incinemas' => $hasFile ? MediaStatus::Available : MediaStatus::Missing,
            'announced' => MediaStatus::Announced,
            'tba'       => MediaStatus::Announced,
            'deleted'   => MediaStatus::Unknown,
            default     => MediaStatus::Unknown,
        };
    }

    /**
     * Normalize Radarr queue item status to DownloadStatus.
     *
     * @param string $status Radarr queue status
     * @param string|null $trackedDownloadStatus Additional status info (ok, warning, error)
     * @param string|null $trackedDownloadState Download state (downloading, importPending, imported, etc.)
     */
    public static function downloadFromRadarrQueue(
        string $status,
        ?string $trackedDownloadStatus = null,
        ?string $trackedDownloadState = null,
    ): DownloadStatus {
        // Handle tracked download state for more specific status
        if ($trackedDownloadState !== null) {
            $mapped = self::downloadFromRadarrTrackedState($trackedDownloadState);
            if ($mapped !== DownloadStatus::Unknown) {
                // Override with warning/error if tracked status indicates an issue
                if ($trackedDownloadStatus === 'warning') {
                    return DownloadStatus::Warning;
                }
                if ($trackedDownloadStatus === 'error') {
                    return DownloadStatus::Failed;
                }

                return $mapped;
            }
        }

        return self::downloadFromSonarrQueue($status, $trackedDownloadStatus);
    }

    /**
     * Normalize Radarr tracked download state.
     */
    private static function downloadFromRadarrTrackedState(string $state): DownloadStatus
    {
        return match (strtolower($state)) {
            'downloading' => DownloadStatus::Downloading,
            'downloadfailed', 'downloadfailedpending' => DownloadStatus::Failed,
            'importpending' => DownloadStatus::Importing,
            'importing'     => DownloadStatus::Importing,
            'imported'      => DownloadStatus::Completed,
            'importfailed'  => DownloadStatus::Failed,
            default         => DownloadStatus::Unknown,
        };
    }

    // =========================================================================
    // NZBGet Mappings
    // =========================================================================

    /**
     * Normalize NZBGet group/job status to DownloadStatus.
     *
     * @param string $status NZBGet status (QUEUED, PAUSED, DOWNLOADING, etc.)
     */
    public static function downloadFromNZBGet(string $status): DownloadStatus
    {
        return match (strtoupper($status)) {
            // Queue states
            'QUEUED' => DownloadStatus::Queued,
            'PAUSED' => DownloadStatus::Paused,

            // Download states
            'DOWNLOADING' => DownloadStatus::Downloading,
            'FETCHING'    => DownloadStatus::Downloading,

            // Post-processing: verification
            'PP_QUEUED'          => DownloadStatus::Queued,
            'LOADING_PARS'       => DownloadStatus::Verifying,
            'VERIFYING_SOURCES'  => DownloadStatus::Verifying,
            'REPAIRING'          => DownloadStatus::Verifying,
            'VERIFYING_REPAIRED' => DownloadStatus::Verifying,

            // Post-processing: extraction
            'RENAMING'  => DownloadStatus::Extracting,
            'UNPACKING' => DownloadStatus::Extracting,

            // Post-processing: import
            'MOVING'           => DownloadStatus::Importing,
            'EXECUTING_SCRIPT' => DownloadStatus::Importing,
            'PP_FINISHED'      => DownloadStatus::Importing,

            // Terminal states
            'SUCCESS' => DownloadStatus::Completed,
            'FAILURE' => DownloadStatus::Failed,
            'DELETED' => DownloadStatus::Failed,

            // Unknown
            default => DownloadStatus::Unknown,
        };
    }

    /**
     * Normalize NZBGet history item status to DownloadStatus.
     *
     * @param string $status NZBGet history status
     */
    public static function downloadFromNZBGetHistory(string $status): DownloadStatus
    {
        return match (strtoupper($status)) {
            'SUCCESS'        => DownloadStatus::Completed,
            'SUCCESS/ALL'    => DownloadStatus::Completed,
            'SUCCESS/UNPACK' => DownloadStatus::Completed,
            'SUCCESS/MARK'   => DownloadStatus::Completed,
            'SUCCESS/GOOD'   => DownloadStatus::Completed,
            'FAILURE'        => DownloadStatus::Failed,
            'FAILURE/UNPACK' => DownloadStatus::Failed,
            'FAILURE/PAR'    => DownloadStatus::Failed,
            'FAILURE/MOVE'   => DownloadStatus::Failed,
            'FAILURE/SCRIPT' => DownloadStatus::Failed,
            'FAILURE/DISK'   => DownloadStatus::Failed,
            'FAILURE/HEALTH' => DownloadStatus::Failed,
            'FAILURE/BAD'    => DownloadStatus::Failed,
            'DELETED'        => DownloadStatus::Failed,
            'DELETED/DUPE'   => DownloadStatus::Failed,
            'DELETED/MANUAL' => DownloadStatus::Failed,
            default          => DownloadStatus::Unknown,
        };
    }

    // =========================================================================
    // SABnzbd Mappings
    // =========================================================================

    /**
     * Normalize SABnzbd status to DownloadStatus.
     */
    public static function downloadFromSABnzbd(string $status): DownloadStatus
    {
        return match (strtolower($status)) {
            'queued'      => DownloadStatus::Queued,
            'paused'      => DownloadStatus::Paused,
            'downloading' => DownloadStatus::Downloading,
            'extracting'  => DownloadStatus::Extracting,
            'verifying'   => DownloadStatus::Verifying,
            'repairing'   => DownloadStatus::Verifying,
            'moving'      => DownloadStatus::Importing,
            'running'     => DownloadStatus::Importing,
            'completed'   => DownloadStatus::Completed,
            'failed'      => DownloadStatus::Failed,
            default       => DownloadStatus::Unknown,
        };
    }

    // =========================================================================
    // Jellyseerr/Overseerr Mappings
    // =========================================================================

    /**
     * Normalize Jellyseerr media status to MediaStatus.
     *
     * @param int $status Jellyseerr status code (1=PENDING, 2=APPROVED, 3=DECLINED, 4=AVAILABLE, 5=PARTIALLY_AVAILABLE)
     */
    public static function mediaFromJellyseerr(int $status): MediaStatus
    {
        return match ($status) {
            1       => MediaStatus::Requested,      // PENDING
            2       => MediaStatus::Queued,         // APPROVED
            3       => MediaStatus::Failed,         // DECLINED
            4       => MediaStatus::Available,      // AVAILABLE
            5       => MediaStatus::Downloading,    // PARTIALLY_AVAILABLE
            default => MediaStatus::Unknown,
        };
    }

    /**
     * Normalize Jellyseerr request status to RequestStatus.
     *
     * @param int $status Jellyseerr status code
     */
    public static function requestFromJellyseerr(int $status): RequestStatus
    {
        return match ($status) {
            1       => RequestStatus::Pending,      // PENDING_APPROVAL
            2       => RequestStatus::Approved,     // APPROVED
            3       => RequestStatus::Rejected,     // DECLINED
            4       => RequestStatus::Fulfilled,    // AVAILABLE (request fulfilled)
            5       => RequestStatus::Approved,     // PARTIALLY_AVAILABLE (still in progress)
            default => RequestStatus::Pending,
        };
    }

    /**
     * Normalize Overseerr status (same as Jellyseerr).
     */
    public static function mediaFromOverseerr(int $status): MediaStatus
    {
        return self::mediaFromJellyseerr($status);
    }

    /**
     * Normalize Overseerr request status (same as Jellyseerr).
     */
    public static function requestFromOverseerr(int $status): RequestStatus
    {
        return self::requestFromJellyseerr($status);
    }

    // =========================================================================
    // Torrent Client Mappings
    // =========================================================================

    /**
     * Normalize qBittorrent state to DownloadStatus.
     */
    public static function downloadFromQBittorrent(string $state): DownloadStatus
    {
        return match (strtolower($state)) {
            'stalledup', 'stalleddn' => DownloadStatus::Warning,
            'pausedup', 'pauseddn' => DownloadStatus::Paused,
            'queuedup', 'queueddn' => DownloadStatus::Queued,
            'downloading', 'metadl', 'forceup', 'forcedn' => DownloadStatus::Downloading,
            'uploading' => DownloadStatus::Completed,
            'checkingup', 'checkingdn', 'checkingresumedata' => DownloadStatus::Verifying,
            'moving' => DownloadStatus::Importing,
            'error', 'missingfiles' => DownloadStatus::Failed,
            default => DownloadStatus::Unknown,
        };
    }

    /**
     * Normalize Transmission status to DownloadStatus.
     */
    public static function downloadFromTransmission(int $status): DownloadStatus
    {
        return match ($status) {
            0       => DownloadStatus::Paused,      // TR_STATUS_STOPPED
            1       => DownloadStatus::Queued,      // TR_STATUS_CHECK_WAIT
            2       => DownloadStatus::Verifying,   // TR_STATUS_CHECK
            3       => DownloadStatus::Queued,      // TR_STATUS_DOWNLOAD_WAIT
            4       => DownloadStatus::Downloading, // TR_STATUS_DOWNLOAD
            5       => DownloadStatus::Queued,      // TR_STATUS_SEED_WAIT
            6       => DownloadStatus::Completed,   // TR_STATUS_SEED
            default => DownloadStatus::Unknown,
        };
    }

    /**
     * Normalize Deluge state to DownloadStatus.
     */
    public static function downloadFromDeluge(string $state): DownloadStatus
    {
        return match (strtolower($state)) {
            'queued'      => DownloadStatus::Queued,
            'paused'      => DownloadStatus::Paused,
            'downloading' => DownloadStatus::Downloading,
            'seeding'     => DownloadStatus::Completed,
            'checking'    => DownloadStatus::Verifying,
            'moving'      => DownloadStatus::Importing,
            'error'       => DownloadStatus::Failed,
            default       => DownloadStatus::Unknown,
        };
    }
}
