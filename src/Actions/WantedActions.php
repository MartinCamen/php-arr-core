<?php

namespace MartinCamen\ArrCore\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\WantedEndpoint;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\ArrCore\Data\Options\WantedOptions;

/**
 * @link https://wiki.servarr.com/sonarr/api
 * @link https://radarr.video/docs/api/#/Wanted
 */
readonly class WantedActions
{
    public function __construct(protected RestClientInterface $client) {}

    /**
     * Get missing episodes (monitored, not downloaded).
     *
     * @link https://radarr.video/docs/api/#/Wanted/get_api_v3_wanted_missing
     *
     * @return array<string, mixed>
     */
    public function missing(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?WantedOptions $filters = null,
    ): array {
        $params = array_merge(
            $pagination?->toArray() ?? PaginationOptions::make()->toArray(),
            $sort?->toArray() ?? [],
            $filters?->toArray() ?? [],
        );

        return $this->client->get(WantedEndpoint::Missing, $params);
    }

    /** @return array<string, mixed> */
    public function allMissing(?WantedOptions $filters = null): array
    {
        $results = [];
        $page = 1;
        $pageSize = 100;

        do {
            $result = $this->missing(
                new PaginationOptions($page, $pageSize),
                null,
                $filters,
            );
            $records = $result['records'] ?? [];
            $results = array_merge($results, $records);
            $totalRecords = $result['totalRecords'] ?? 0;
            $page++;
        } while (count($results) < $totalRecords);

        return $results;
    }

    /**
     * Get items below quality cutoff.
     *
     * @link https://radarr.video/docs/api/#/Wanted/get_api_v3_wanted_cutoff
     *
     * @return array<string, mixed>
     */
    public function cutoff(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?WantedOptions $filters = null,
    ): array {
        $params = array_merge(
            $pagination?->toArray() ?? PaginationOptions::make()->toArray(),
            $sort?->toArray() ?? [],
            $filters?->toArray() ?? [],
        );

        return $this->client->get(WantedEndpoint::Cutoff, $params);
    }

    /**
     * Get all episodes below quality cutoff.
     *
     * @return array<string, mixed>
     */
    public function allCutoff(?WantedOptions $filters = null): array
    {
        $items = [];
        $page = 1;
        $pageSize = 100;

        do {
            $result = $this->cutoff(
                new PaginationOptions($page, $pageSize),
                null,
                $filters,
            );
            $records = $result['records'] ?? [];
            $items = array_merge($items, $records);
            $totalRecords = $result['totalRecords'] ?? 0;
            $page++;
        } while (count($items) < $totalRecords);

        return $items;
    }
}
