<?php

namespace MartinCamen\ArrCore\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\QueueEndpoint;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\RequestOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\ArrCore\Data\Responses\QueueStatus;

/**
 * @link https://sonarr.tv/docs/api/#v3/
 * @link https://radarr.video/docs/api/#/Queue
 */
readonly class QueueActions
{
    public function __construct(protected RestClientInterface $client) {}

    /** @return array<string, mixed> */
    public function getAll(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?RequestOptions $filters = null,
    ): array {
        $params = array_merge(
            $pagination?->toArray() ?? (new PaginationOptions(pageSize: 50))->toArray(),
            $sort?->toArray() ?? [],
            $filters?->toArray() ?? [],
        );

        return $this->client->get(QueueEndpoint::All, $params);
    }

    public function status(): QueueStatus
    {
        $result = $this->client->get(QueueEndpoint::Status);

        return QueueStatus::fromArray($result);
    }

    /**
     * Delete item from queue.
     */
    public function delete(
        int $id,
        bool $removeFromClient = true,
        bool $blocklist = false,
        bool $skipRedownload = false,
        bool $changeCategory = false,
    ): void {
        $this->client->delete(QueueEndpoint::ById, [
            'id'               => $id,
            'removeFromClient' => $removeFromClient,
            'blocklist'        => $blocklist,
            'skipRedownload'   => $skipRedownload,
            'changeCategory'   => $changeCategory,
        ]);
    }

    /**
     * Bulk delete items from queue.
     *
     * @param array<int, int> $ids
     */
    public function bulkDelete(
        array $ids,
        bool $removeFromClient = true,
        bool $blocklist = false,
        bool $skipRedownload = false,
        bool $changeCategory = false,
    ): void {
        $this->client->delete(QueueEndpoint::Bulk, [
            'ids'              => $ids,
            'removeFromClient' => $removeFromClient,
            'blocklist'        => $blocklist,
            'skipRedownload'   => $skipRedownload,
            'changeCategory'   => $changeCategory,
        ]);
    }
}
