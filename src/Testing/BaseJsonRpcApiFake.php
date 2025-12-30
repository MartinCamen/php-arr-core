<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing;

use MartinCamen\ArrCore\Client\JsonRpcClientInterface;
use MartinCamen\ArrCore\Contract\JsonRpcApiFake;
use MartinCamen\ArrCore\Contract\JsonRpcEndpoint;
use PHPUnit\Framework\Assert;

/**
 * Base class for JSON-RPC API fakes.
 *
 * Provides common functionality for recording calls and managing responses.
 * Unlike REST API fakes, JSON-RPC uses method names and positional params.
 */
class BaseJsonRpcApiFake implements JsonRpcApiFake
{
    /** @var array<int, array{method: string, params: array<int, mixed>}> */
    protected array $calls = [];

    /** @param array<string, mixed> $responses */
    public function __construct(
        protected array $responses = [],
    ) {}

    protected function getFakeClient(): JsonRpcClientInterface
    {
        return new FakeJsonRpcClient($this);
    }

    /** @param array<int, mixed> $params */
    public function recordEndpointCall(JsonRpcEndpoint $endpoint, array $params): void
    {
        $this->recordCall($endpoint->value(), $params);
    }

    public function getResponseFor(JsonRpcEndpoint $endpoint): mixed
    {
        return $this->getResponseForMethod($endpoint->value()) ?? $endpoint->defaultResponse();
    }

    /**
     * Record a call to the API.
     *
     * @param array<int, mixed> $params
     */
    public function recordCall(string $method, array $params): void
    {
        $this->calls[] = [
            'method' => $method,
            'params' => $params,
        ];
    }

    /**
     * Get the response for a method name.
     */
    public function getResponseForMethod(string $method): mixed
    {
        return $this->responses[$method] ?? null;
    }

    /**
     * Set a response for a specific method.
     */
    public function setResponse(string $method, mixed $response): static
    {
        $this->responses[$method] = $response;

        return $this;
    }

    /** @return array<int, array{method: string, params: array<int, mixed>}> */
    public function getCalls(): array
    {
        return $this->calls;
    }

    public function assertCalled(string $method): void
    {
        $called = $this->hasCalled($method);

        Assert::assertTrue($called, sprintf('Expected method [%s] to be called, but it was not.', $method));
    }

    public function assertNotCalled(string $method): void
    {
        $called = $this->hasCalled($method);

        Assert::assertFalse($called, sprintf('Expected method [%s] not to be called, but it was.', $method));
    }

    /** @param array<int, mixed> $params */
    public function assertCalledWith(string $method, array $params): void
    {
        $found = null;

        foreach ($this->calls as $call) {
            if ($call['method'] === $method && $call['params'] === $params) {
                $found = $call;

                break;
            }
        }

        Assert::assertNotNull(
            $found,
            sprintf('Expected method [%s] to be called with params ', $method) . json_encode($params) . ', but it was not.',
        );
    }

    public function assertCalledTimes(string $method, int $times): void
    {
        $count = $this->getCallCount($method);

        Assert::assertEquals(
            $times,
            $count,
            sprintf('Expected method [%s] to be called %d times, but it was called %d times.', $method, $times, $count),
        );
    }

    public function assertNothingCalled(): void
    {
        Assert::assertEmpty($this->calls, 'Expected no methods to be called, but some were.');
    }

    private function hasCalled(string $method): bool
    {
        foreach ($this->calls as $call) {
            if ($call['method'] === $method) {
                return true;
            }
        }

        return false;
    }

    private function getCallCount(string $method): int
    {
        $count = 0;

        foreach ($this->calls as $call) {
            if ($call['method'] === $method) {
                $count++;
            }
        }

        return $count;
    }
}
