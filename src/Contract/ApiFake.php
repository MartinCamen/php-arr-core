<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Interface for API fakes that record calls and provide responses.
 *
 * This enables the FakeRestClient to work with any API fake implementation.
 */
interface ApiFake
{
    /**
     * Record a call to an endpoint.
     *
     * @param array<string, mixed> $params
     */
    public function recordEndpointCall(string $method, Endpoint $endpoint, array $params): void;

    /**
     * Get the response for an endpoint.
     */
    public function getResponseFor(Endpoint $endpoint): mixed;
}
