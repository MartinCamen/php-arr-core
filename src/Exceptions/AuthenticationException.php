<?php

namespace MartinCamen\ArrCore\Exceptions;

class AuthenticationException extends ArrCoreException
{
    public static function invalidApiKey(): self
    {
        return new self('Invalid API key provided. Please check your configuration.');
    }

    public static function unauthorized(): self
    {
        return new self('Unauthorized operation.');
    }
}
