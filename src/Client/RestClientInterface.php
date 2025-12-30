<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Client;

use MartinCamen\ArrCore\Contract\Endpoint;

/**
 * All *arr service REST clients should implement this interface
 * to enable shared testing utilities.
 */
interface RestClientInterface
{
    /** @param array<string, mixed> $params */
    public function get(Endpoint $endpoint, array $params = []): mixed;

    /** @param array<string, mixed> $data */
    public function post(Endpoint $endpoint, array $data = []): mixed;

    /** @param array<string, mixed> $data */
    public function put(Endpoint $endpoint, array $data = []): mixed;

    /**
     * @param array<string, mixed> $params
     */
    public function delete(Endpoint $endpoint, array $params = []): mixed;
}
