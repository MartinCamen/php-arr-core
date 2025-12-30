<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Interface for API endpoint enums.
 *
 * All *arr service endpoint enums should implement this interface
 * to enable shared testing utilities and client implementations.
 */
interface Endpoint
{
    /**
     * Get the endpoint path, optionally with parameter substitution.
     *
     * @param array<string, mixed> $params
     */
    public function path(array $params = []): string;

    /**
     * Get the default response for this endpoint (used by fakes).
     */
    public function defaultResponse(): mixed;
}
