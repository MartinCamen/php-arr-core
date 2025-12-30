<?php

namespace MartinCamen\ArrCore\Testing\Traits;

trait HandlesFakeCalls
{
    /**
     * Record a method call for assertions.
     *
     * @param array<string, mixed> $params
     */
    private function recordCall(string $method, array $params): void
    {
        $this->calls[$method][] = [
            'params' => $params,
        ];
    }

    /**
     * Get all recorded calls.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getCalls(): array
    {
        return $this->calls;
    }
}
