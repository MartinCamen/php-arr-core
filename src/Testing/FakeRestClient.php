<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Contract\ApiFake;
use MartinCamen\ArrCore\Contract\Endpoint;

/**
 * Fake REST client for testing.
 *
 * Records all API calls and returns configured responses.
 */
class FakeRestClient implements RestClientInterface
{
    public function __construct(protected ApiFake $fake) {}

    /** @param array<string, mixed> $params */
    public function get(Endpoint $endpoint, array $params = []): mixed
    {
        $this->fake->recordEndpointCall('get', $endpoint, $params);

        return $this->fake->getResponseFor($endpoint);
    }

    /** @param array<string, mixed> $data */
    public function post(Endpoint $endpoint, array $data = []): mixed
    {
        $this->fake->recordEndpointCall('post', $endpoint, $data);

        return $this->fake->getResponseFor($endpoint);
    }

    /** @param array<string, mixed> $data */
    public function put(Endpoint $endpoint, array $data = []): mixed
    {
        $this->fake->recordEndpointCall('put', $endpoint, $data);

        return $this->fake->getResponseFor($endpoint);
    }

    /** @param array<string, mixed> $params */
    public function delete(Endpoint $endpoint, array $params = []): mixed
    {
        $this->fake->recordEndpointCall('delete', $endpoint, $params);

        return $this->fake->getResponseFor($endpoint);
    }
}
