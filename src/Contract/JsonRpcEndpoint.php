<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Interface for JSON-RPC endpoint enums.
 */
interface JsonRpcEndpoint
{
    /**
     * Get the RPC method name.
     */
    public function value(): string;

    /**
     * Get the default response for this endpoint (used by fakes).
     */
    /** @return string|array<string, mixed>|null|bool */
    public function defaultResponse(): string|array|null|bool;
}
