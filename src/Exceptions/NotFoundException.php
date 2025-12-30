<?php

namespace MartinCamen\ArrCore\Exceptions;

class NotFoundException extends ArrCoreException
{
    public static function resourceNotFound(string $resource): self
    {
        return new self(
            sprintf('Resource not found: %s', $resource),
        );
    }
}
