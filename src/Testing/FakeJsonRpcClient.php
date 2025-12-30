<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing;

use MartinCamen\ArrCore\Client\JsonRpcClientInterface;
use MartinCamen\ArrCore\Contract\JsonRpcApiFake;
use MartinCamen\ArrCore\Contract\JsonRpcEndpoint;

/**
 * Fake JSON-RPC client for testing.
 *
 * Records all API calls and returns configured responses.
 */
class FakeJsonRpcClient implements JsonRpcClientInterface
{
    private int $requestId = 0;

    public function __construct(
        protected JsonRpcApiFake $fake,
    ) {}

    /** @param array<int, mixed> $params */
    public function call(JsonRpcEndpoint $endpoint, array $params = []): mixed
    {
        $this->requestId++;
        $this->fake->recordEndpointCall($endpoint, $params);

        return $this->fake->getResponseFor($endpoint);
    }

    public function getRequestId(): int
    {
        return $this->requestId;
    }
}
