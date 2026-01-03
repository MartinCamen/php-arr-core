<?php

namespace MartinCamen\ArrCore\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\CalendarEndpoint;
use MartinCamen\ArrCore\Data\Options\RequestOptions;

/**
 * @link https://radarr.video/docs/api/#/Calendar
 * @link https://wiki.servarr.com/sonarr/api
 */
readonly class CalendarActions
{
    public function __construct(private RestClientInterface $client) {}

    /**
     * Get upcoming movies within a date range.
     *
     * @link https://radarr.video/docs/api/#/Calendar/get_api_v3_calendar
     *
     * @return array<int, array<string, mixed>> $params
     */
    public function getAll(?RequestOptions $options = null): array
    {
        return $this->client->get(CalendarEndpoint::All, $options?->toArray() ?? []);
    }

    /**
     * Get calendar event by ID.
     *
     * @link https://radarr.video/docs/api/#/Calendar/get_api_v3_calendar__id
     *
     * @return array<string, mixed> $params
     */
    public function getById(int $id): array
    {
        return $this->client->get(CalendarEndpoint::ById, ['id' => $id]);
    }
}
