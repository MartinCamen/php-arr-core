<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Interface for JSON-RPC API fakes that record calls and provide responses.
 */
interface JsonRpcApiFake
{
    /**
     * Record a call to an endpoint.
     *
     * @param array<int, mixed> $params
     */
    public function recordEndpointCall(JsonRpcEndpoint $endpoint, array $params): void;

    /**
     * Get the response for an endpoint.
     */
    public function getResponseFor(JsonRpcEndpoint $endpoint): mixed;
}
