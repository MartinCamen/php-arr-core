<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Mapping;

use MartinCamen\ArrCore\Enum\DownloadStatus;
use MartinCamen\ArrCore\Enum\MediaStatus;
use MartinCamen\ArrCore\Enum\RequestStatus;
use MartinCamen\ArrCore\Mapping\StatusNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StatusNormalizerTest extends TestCase
{
    // =========================================================================
    // Sonarr Tests
    // =========================================================================

    #[Test]
    #[DataProvider('sonarrMediaStatusProvider')]
    public function normalizesSonarrMediaStatus(
        string $status,
        bool $hasFiles,
        MediaStatus $expected,
    ): void {
        $result = StatusNormalizer::mediaFromSonarr($status, $hasFiles);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, bool, MediaStatus}>
     */
    public static function sonarrMediaStatusProvider(): array
    {
        return [
            'continuing with files'    => ['continuing', true, MediaStatus::Available],
            'continuing without files' => ['continuing', false, MediaStatus::Missing],
            'ended with files'         => ['ended', true, MediaStatus::Available],
            'ended without files'      => ['ended', false, MediaStatus::Missing],
            'upcoming'                 => ['upcoming', false, MediaStatus::Announced],
            'deleted'                  => ['deleted', false, MediaStatus::Unknown],
            'unknown status'           => ['invalid', false, MediaStatus::Unknown],
            'case insensitive'         => ['CONTINUING', true, MediaStatus::Available],
        ];
    }

    #[Test]
    #[DataProvider('sonarrQueueStatusProvider')]
    public function normalizesSonarrQueueStatus(
        string $status,
        ?string $trackedStatus,
        DownloadStatus $expected,
    ): void {
        $result = StatusNormalizer::downloadFromSonarrQueue($status, $trackedStatus);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, string|null, DownloadStatus}>
     */
    public static function sonarrQueueStatusProvider(): array
    {
        return [
            'queued'                    => ['queued', null, DownloadStatus::Queued],
            'paused'                    => ['paused', null, DownloadStatus::Paused],
            'downloading'               => ['downloading', null, DownloadStatus::Downloading],
            'completed'                 => ['completed', null, DownloadStatus::Completed],
            'failed'                    => ['failed', null, DownloadStatus::Failed],
            'warning'                   => ['warning', null, DownloadStatus::Warning],
            'delay'                     => ['delay', null, DownloadStatus::Queued],
            'downloadclientunavailable' => ['downloadclientunavailable', null, DownloadStatus::Warning],
            'tracked status ok'         => ['downloading', 'ok', DownloadStatus::Downloading],
            'tracked status warning'    => ['downloading', 'warning', DownloadStatus::Warning],
            'tracked status error'      => ['downloading', 'error', DownloadStatus::Failed],
            'unknown status'            => ['invalid', null, DownloadStatus::Unknown],
        ];
    }

    // =========================================================================
    // Radarr Tests
    // =========================================================================

    #[Test]
    #[DataProvider('radarrMediaStatusProvider')]
    public function normalizesRadarrMediaStatus(
        string $status,
        bool $hasFile,
        MediaStatus $expected,
    ): void {
        $result = StatusNormalizer::mediaFromRadarr($status, $hasFile);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, bool, MediaStatus}>
     */
    public static function radarrMediaStatusProvider(): array
    {
        return [
            'released with file'     => ['released', true, MediaStatus::Available],
            'released without file'  => ['released', false, MediaStatus::Missing],
            'incinemas with file'    => ['incinemas', true, MediaStatus::Available],
            'incinemas without file' => ['incinemas', false, MediaStatus::Missing],
            'announced'              => ['announced', false, MediaStatus::Announced],
            'tba'                    => ['tba', false, MediaStatus::Announced],
            'deleted'                => ['deleted', false, MediaStatus::Unknown],
            'unknown status'         => ['invalid', false, MediaStatus::Unknown],
            'case insensitive'       => ['RELEASED', true, MediaStatus::Available],
        ];
    }

    // =========================================================================
    // NZBGet Tests
    // =========================================================================

    #[Test]
    #[DataProvider('nzbgetStatusProvider')]
    public function normalizesNZBGetStatus(string $status, DownloadStatus $expected): void
    {
        $result = StatusNormalizer::downloadFromNZBGet($status);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, DownloadStatus}>
     */
    public static function nzbgetStatusProvider(): array
    {
        return [
            // Queue states
            'queued' => ['QUEUED', DownloadStatus::Queued],
            'paused' => ['PAUSED', DownloadStatus::Paused],

            // Download states
            'downloading' => ['DOWNLOADING', DownloadStatus::Downloading],
            'fetching'    => ['FETCHING', DownloadStatus::Downloading],

            // Post-processing verification
            'pp_queued'          => ['PP_QUEUED', DownloadStatus::Queued],
            'loading_pars'       => ['LOADING_PARS', DownloadStatus::Verifying],
            'verifying_sources'  => ['VERIFYING_SOURCES', DownloadStatus::Verifying],
            'repairing'          => ['REPAIRING', DownloadStatus::Verifying],
            'verifying_repaired' => ['VERIFYING_REPAIRED', DownloadStatus::Verifying],

            // Post-processing extraction
            'renaming'  => ['RENAMING', DownloadStatus::Extracting],
            'unpacking' => ['UNPACKING', DownloadStatus::Extracting],

            // Post-processing import
            'moving'           => ['MOVING', DownloadStatus::Importing],
            'executing_script' => ['EXECUTING_SCRIPT', DownloadStatus::Importing],
            'pp_finished'      => ['PP_FINISHED', DownloadStatus::Importing],

            // Terminal states
            'success' => ['SUCCESS', DownloadStatus::Completed],
            'failure' => ['FAILURE', DownloadStatus::Failed],
            'deleted' => ['DELETED', DownloadStatus::Failed],

            // Unknown
            'unknown' => ['INVALID', DownloadStatus::Unknown],

            // Case sensitivity
            'lowercase' => ['downloading', DownloadStatus::Downloading],
        ];
    }

    #[Test]
    #[DataProvider('nzbgetHistoryStatusProvider')]
    public function normalizesNZBGetHistoryStatus(string $status, DownloadStatus $expected): void
    {
        $result = StatusNormalizer::downloadFromNZBGetHistory($status);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, DownloadStatus}>
     */
    public static function nzbgetHistoryStatusProvider(): array
    {
        return [
            'success'        => ['SUCCESS', DownloadStatus::Completed],
            'success/all'    => ['SUCCESS/ALL', DownloadStatus::Completed],
            'success/unpack' => ['SUCCESS/UNPACK', DownloadStatus::Completed],
            'success/mark'   => ['SUCCESS/MARK', DownloadStatus::Completed],
            'success/good'   => ['SUCCESS/GOOD', DownloadStatus::Completed],
            'failure'        => ['FAILURE', DownloadStatus::Failed],
            'failure/unpack' => ['FAILURE/UNPACK', DownloadStatus::Failed],
            'failure/par'    => ['FAILURE/PAR', DownloadStatus::Failed],
            'failure/move'   => ['FAILURE/MOVE', DownloadStatus::Failed],
            'failure/script' => ['FAILURE/SCRIPT', DownloadStatus::Failed],
            'failure/disk'   => ['FAILURE/DISK', DownloadStatus::Failed],
            'failure/health' => ['FAILURE/HEALTH', DownloadStatus::Failed],
            'failure/bad'    => ['FAILURE/BAD', DownloadStatus::Failed],
            'deleted'        => ['DELETED', DownloadStatus::Failed],
            'deleted/dupe'   => ['DELETED/DUPE', DownloadStatus::Failed],
            'deleted/manual' => ['DELETED/MANUAL', DownloadStatus::Failed],
            'unknown'        => ['INVALID', DownloadStatus::Unknown],
        ];
    }

    // =========================================================================
    // SABnzbd Tests
    // =========================================================================

    #[Test]
    #[DataProvider('sabnzbdStatusProvider')]
    public function normalizesSABnzbdStatus(string $status, DownloadStatus $expected): void
    {
        $result = StatusNormalizer::downloadFromSABnzbd($status);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, DownloadStatus}>
     */
    public static function sabnzbdStatusProvider(): array
    {
        return [
            'queued'      => ['queued', DownloadStatus::Queued],
            'paused'      => ['paused', DownloadStatus::Paused],
            'downloading' => ['downloading', DownloadStatus::Downloading],
            'extracting'  => ['extracting', DownloadStatus::Extracting],
            'verifying'   => ['verifying', DownloadStatus::Verifying],
            'repairing'   => ['repairing', DownloadStatus::Verifying],
            'moving'      => ['moving', DownloadStatus::Importing],
            'running'     => ['running', DownloadStatus::Importing],
            'completed'   => ['completed', DownloadStatus::Completed],
            'failed'      => ['failed', DownloadStatus::Failed],
            'unknown'     => ['invalid', DownloadStatus::Unknown],
        ];
    }

    // =========================================================================
    // Jellyseerr Tests
    // =========================================================================

    #[Test]
    #[DataProvider('jellyseerrMediaStatusProvider')]
    public function normalizesJellyseerrMediaStatus(int $status, MediaStatus $expected): void
    {
        $result = StatusNormalizer::mediaFromJellyseerr($status);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{int, MediaStatus}>
     */
    public static function jellyseerrMediaStatusProvider(): array
    {
        return [
            'pending'             => [1, MediaStatus::Requested],
            'approved'            => [2, MediaStatus::Queued],
            'declined'            => [3, MediaStatus::Failed],
            'available'           => [4, MediaStatus::Available],
            'partially_available' => [5, MediaStatus::Downloading],
            'unknown'             => [99, MediaStatus::Unknown],
        ];
    }

    #[Test]
    #[DataProvider('jellyseerrRequestStatusProvider')]
    public function normalizesJellyseerrRequestStatus(int $status, RequestStatus $expected): void
    {
        $result = StatusNormalizer::requestFromJellyseerr($status);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{int, RequestStatus}>
     */
    public static function jellyseerrRequestStatusProvider(): array
    {
        return [
            'pending'             => [1, RequestStatus::Pending],
            'approved'            => [2, RequestStatus::Approved],
            'declined'            => [3, RequestStatus::Rejected],
            'available'           => [4, RequestStatus::Fulfilled],
            'partially_available' => [5, RequestStatus::Approved],
            'unknown'             => [99, RequestStatus::Pending],
        ];
    }

    // =========================================================================
    // Torrent Client Tests
    // =========================================================================

    #[Test]
    #[DataProvider('qbittorrentStatusProvider')]
    public function normalizesQBittorrentStatus(string $state, DownloadStatus $expected): void
    {
        $result = StatusNormalizer::downloadFromQBittorrent($state);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, DownloadStatus}>
     */
    public static function qbittorrentStatusProvider(): array
    {
        return [
            'stalledUp'          => ['stalledUp', DownloadStatus::Warning],
            'stalledDn'          => ['stalledDn', DownloadStatus::Warning],
            'pausedUp'           => ['pausedUp', DownloadStatus::Paused],
            'pausedDn'           => ['pausedDn', DownloadStatus::Paused],
            'queuedUp'           => ['queuedUp', DownloadStatus::Queued],
            'queuedDn'           => ['queuedDn', DownloadStatus::Queued],
            'downloading'        => ['downloading', DownloadStatus::Downloading],
            'metaDl'             => ['metaDl', DownloadStatus::Downloading],
            'forcedUp'           => ['forceUp', DownloadStatus::Downloading],
            'forcedDn'           => ['forceDn', DownloadStatus::Downloading],
            'uploading'          => ['uploading', DownloadStatus::Completed],
            'checkingUp'         => ['checkingUp', DownloadStatus::Verifying],
            'checkingDn'         => ['checkingDn', DownloadStatus::Verifying],
            'checkingResumeData' => ['checkingResumeData', DownloadStatus::Verifying],
            'moving'             => ['moving', DownloadStatus::Importing],
            'error'              => ['error', DownloadStatus::Failed],
            'missingFiles'       => ['missingFiles', DownloadStatus::Failed],
            'unknown'            => ['invalid', DownloadStatus::Unknown],
        ];
    }

    #[Test]
    #[DataProvider('transmissionStatusProvider')]
    public function normalizesTransmissionStatus(int $status, DownloadStatus $expected): void
    {
        $result = StatusNormalizer::downloadFromTransmission($status);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{int, DownloadStatus}>
     */
    public static function transmissionStatusProvider(): array
    {
        return [
            'stopped'       => [0, DownloadStatus::Paused],
            'check_wait'    => [1, DownloadStatus::Queued],
            'check'         => [2, DownloadStatus::Verifying],
            'download_wait' => [3, DownloadStatus::Queued],
            'download'      => [4, DownloadStatus::Downloading],
            'seed_wait'     => [5, DownloadStatus::Queued],
            'seed'          => [6, DownloadStatus::Completed],
            'unknown'       => [99, DownloadStatus::Unknown],
        ];
    }

    #[Test]
    #[DataProvider('delugeStatusProvider')]
    public function normalizesDelugeStatus(string $state, DownloadStatus $expected): void
    {
        $result = StatusNormalizer::downloadFromDeluge($state);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, DownloadStatus}>
     */
    public static function delugeStatusProvider(): array
    {
        return [
            'queued'      => ['queued', DownloadStatus::Queued],
            'paused'      => ['paused', DownloadStatus::Paused],
            'downloading' => ['downloading', DownloadStatus::Downloading],
            'seeding'     => ['seeding', DownloadStatus::Completed],
            'checking'    => ['checking', DownloadStatus::Verifying],
            'moving'      => ['moving', DownloadStatus::Importing],
            'error'       => ['error', DownloadStatus::Failed],
            'unknown'     => ['invalid', DownloadStatus::Unknown],
        ];
    }

    // =========================================================================
    // Consistency Tests
    // =========================================================================

    #[Test]
    public function overseerrMapsIdenticallyToJellyseerr(): void
    {
        for ($i = 0; $i <= 10; $i++) {
            $this->assertSame(
                StatusNormalizer::mediaFromJellyseerr($i),
                StatusNormalizer::mediaFromOverseerr($i),
                "Overseerr media status {$i} should map identically to Jellyseerr",
            );

            $this->assertSame(
                StatusNormalizer::requestFromJellyseerr($i),
                StatusNormalizer::requestFromOverseerr($i),
                "Overseerr request status {$i} should map identically to Jellyseerr",
            );
        }
    }

    #[Test]
    public function radarrQueueMapsIdenticallyToSonarr(): void
    {
        $statuses = ['queued', 'paused', 'downloading', 'completed', 'failed', 'warning'];

        foreach ($statuses as $status) {
            $this->assertSame(
                StatusNormalizer::downloadFromSonarrQueue($status),
                StatusNormalizer::downloadFromRadarrQueue($status),
                "Radarr queue status '{$status}' should map identically to Sonarr",
            );
        }
    }
}
