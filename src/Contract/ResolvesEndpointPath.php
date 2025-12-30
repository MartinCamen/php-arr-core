<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Trait for endpoint enums that resolve paths with parameter substitution.
 *
 * This trait implements the path() method required by the Endpoint interface,
 * replacing placeholders like {id} with actual values from the params array.
 *
 * @property string $value The enum's string value (endpoint path template)
 */
trait ResolvesEndpointPath
{
    /** @param array<string, mixed> $params */
    public function path(array $params = []): string
    {
        $path = $this->value;

        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", (string) $value, $path);
        }

        return $path;
    }
}
