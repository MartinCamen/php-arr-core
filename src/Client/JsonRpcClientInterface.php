<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Client;

use MartinCamen\ArrCore\Contract\JsonRpcEndpoint;

interface JsonRpcClientInterface
{
    /**
     * Call a JSON-RPC method
     *
     * @param array<int, mixed> $params
     */
    public function call(JsonRpcEndpoint $endpoint, array $params = []): mixed;

    /** Get the current request ID */
    public function getRequestId(): int;
}
