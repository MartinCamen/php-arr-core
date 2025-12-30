<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing\Factories;

/**
 * Base factory for *arr service download/queue records.
 *
 * Provides shared structure for Radarr and Sonarr download factories.
 * Service-specific factories should extend this and override getServiceDefaults().
 */
abstract class ArrDownloadFactory
{
    /**
     * Create a single download record.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function make(int $id = 1, array $overrides = []): array
    {
        return array_merge(
            static::getSharedDefaults($id),
            static::getServiceDefaults($id),
            $overrides,
        );
    }

    /**
     * Create multiple download records.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function makeMany(int $count = 5): array
    {
        $records = [];

        for ($i = 1; $i <= $count; $i++) {
            $records[] = static::make($i);
        }

        return $records;
    }

    /**
     * Create a paginated response structure.
     *
     * @return array<string, mixed>
     */
    public static function makePaginatedResponse(
        int $count = 5,
        int $page = 1,
        int $pageSize = 10,
        int $totalRecords = 5,
    ): array {
        return [
            'page'         => $page,
            'pageSize'     => $pageSize,
            'totalRecords' => $totalRecords,
            'records'      => static::makeMany($count),
        ];
    }

    /**
     * Create a completed download record.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function makeCompleted(int $id = 1, array $overrides = []): array
    {
        return static::make($id, array_merge([
            'status'                => 'completed',
            'trackedDownloadStatus' => 'ok',
            'trackedDownloadState'  => 'importPending',
            'sizeleft'              => 0,
            'timeleft'              => null,
        ], $overrides));
    }

    /**
     * Create a download record with an error.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function makeWithError(int $id = 1, array $overrides = []): array
    {
        return static::make($id, array_merge([
            'trackedDownloadStatus' => 'warning',
            'errorMessage'          => 'Download verification failed',
            'statusMessages'        => [
                ['title' => 'Download failed', 'messages' => ['Verification failed']],
            ],
        ], $overrides));
    }

    /**
     * Get shared default attributes common to all *arr services.
     *
     * @return array<string, mixed>
     */
    protected static function getSharedDefaults(int $id): array
    {
        return [
            'id'                      => $id,
            'status'                  => 'downloading',
            'trackedDownloadStatus'   => 'ok',
            'trackedDownloadState'    => 'downloading',
            'quality'                 => ['quality' => ['name' => 'Bluray-1080p']],
            'timeleft'                => '00:30:00',
            'estimatedCompletionTime' => '2024-01-01T12:00:00Z',
            'downloadClient'          => 'SABnzbd',
            'downloadId'              => 'SABnzbd_nzo_' . str_pad((string) $id, 12, '0', STR_PAD_LEFT),
            'protocol'                => 'usenet',
            'indexer'                 => 'NZBGeek',
            'statusMessages'          => [],
            'errorMessage'            => null,
        ];
    }

    /**
     * Get service-specific default attributes.
     *
     * Override this in service-specific factories to provide
     * service-specific fields like movieId, seriesId, etc.
     *
     * @return array<string, mixed>
     */
    abstract protected static function getServiceDefaults(int $id): array;
}
