<?php

namespace MartinCamen\ArrCore\Actions;

use DateTimeInterface;
use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\HistoryEndpoint;
use MartinCamen\ArrCore\Data\Options\HistoryRequestOptions;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;

/**
 * @link https://sonarr.tv/docs/api/#v3/tag/history/GET/api/v3/history
 * @link https://radarr.video/docs/api/#/History
 */
readonly class HistoryActions
{
    public function __construct(protected RestClientInterface $client) {}

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function all(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        array $filters = [],
    ): array {
        $params = array_merge(
            $pagination?->toArray() ?? PaginationOptions::default()->toArray(),
            $sort?->toArray() ?? [],
            $filters,
        );

        return $this->client->get(HistoryEndpoint::All, $params);
    }

    /**
     * Get history since a specific date.
     *
     * @return array<string, mixed>
     *
     * @link https://radarr.video/docs/api/#/History/get_api_v3_history_since
     */
    public function since(
        DateTimeInterface $date,
        ?HistoryRequestOptions $filters = null,
    ): array {
        $params = array_merge(
            ['date' => $date->format('Y-m-d')],
            $filters?->toArray() ?? [],
        );

        return $this->client->get(HistoryEndpoint::Since, $params);
    }

    /**
     * Mark a history item as failed.
     *
     * @link https://radarr.video/docs/api/#/History/post_api_v3_history_failed__id_
     */
    public function markFailed(int $id): void
    {
        $this->client->post(HistoryEndpoint::Failed, ['id' => $id]);
    }
}
