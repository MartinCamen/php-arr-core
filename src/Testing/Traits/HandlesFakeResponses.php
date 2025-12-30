<?php

namespace MartinCamen\ArrCore\Testing;

trait HandlesFakeResponses
{
    /**
     * Set a custom response for a method or key.
     */
    public function setResponse(string $key, mixed $response): self
    {
        $this->responses[$key] = $response;

        return $this;
    }
}
