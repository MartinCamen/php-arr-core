<?php

namespace MartinCamen\ArrCore\Exceptions;

class ValidationException extends ArrCoreException
{
    /** @var array<string, mixed> */
    protected array $errors = [];

    /** @param array<string, mixed>|null $response */
    public static function fromResponse(?array $response): self
    {
        $exception = new self('Validation failed');

        if ($response !== null) {
            $exception->errors = $response;

            if (isset($response['message']) && is_string($response['message'])) {
                $exception->message = $response['message'];
            }
        }

        return $exception;
    }

    /** @return array<string, mixed> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
