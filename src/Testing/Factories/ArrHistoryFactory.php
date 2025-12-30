<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing\Factories;

/**
 * Base factory for *arr service history records.
 *
 * Provides shared structure for Radarr and Sonarr history factories.
 * Service-specific factories should extend this and override getServiceDefaults().
 */
abstract class ArrHistoryFactory
{
    /**
     * Create a single history record.
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
     * Create multiple history records.
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
     * Create a grabbed history record.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function makeGrabbed(int $id = 1, array $overrides = []): array
    {
        return static::make($id, array_merge([
            'eventType' => 'grabbed',
        ], $overrides));
    }

    /**
     * Create an imported history record.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function makeImported(int $id = 1, array $overrides = []): array
    {
        return static::make($id, array_merge([
            'eventType' => 'downloadFolderImported',
        ], $overrides));
    }

    /**
     * Create a failed history record.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function makeFailed(int $id = 1, array $overrides = []): array
    {
        return static::make($id, array_merge([
            'eventType' => 'downloadFailed',
            'data'      => [
                'message' => 'Download failed - verification failed',
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
            'id'         => $id,
            'eventType'  => 'grabbed',
            'quality'    => ['quality' => ['name' => 'Bluray-1080p']],
            'date'       => '2024-01-01T12:00:00Z',
            'downloadId' => 'SABnzbd_nzo_' . str_pad((string) $id, 12, '0', STR_PAD_LEFT),
            'data'       => [
                'indexer'      => 'NZBGeek',
                'releaseGroup' => 'GROUP',
            ],
        ];
    }

    /**
     * Get service-specific default attributes.
     *
     * Override this in service-specific factories to provide
     * service-specific fields like movieId, seriesId, sourceTitle, etc.
     *
     * @return array<string, mixed>
     */
    abstract protected static function getServiceDefaults(int $id): array;
}
