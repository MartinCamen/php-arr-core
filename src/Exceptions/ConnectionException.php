<?php

namespace MartinCamen\ArrCore\Exceptions;

class ConnectionException extends ArrCoreException
{
    public static function failed(string $host, int $port, string $message): self
    {
        return new self(
            sprintf('Failed to connect at %s:%d. Error: %s', $host, $port, $message),
        );
    }
}
