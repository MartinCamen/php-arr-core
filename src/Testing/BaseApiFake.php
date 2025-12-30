<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Contract\ApiFake;
use MartinCamen\ArrCore\Contract\Endpoint;
use PHPUnit\Framework\Assert;

/**
 * Base class for API fakes.
 *
 * Provides common functionality for recording calls and managing responses.
 */
class BaseApiFake implements ApiFake
{
    /** @var array<int, array{method: string, endpoint: string, params: array<string, mixed>}> */
    protected array $calls = [];

    /** @param array<string, mixed> $responses */
    public function __construct(protected array $responses = []) {}

    /**
     * Get the fake REST client for this fake.
     */
    protected function getFakeClient(): RestClientInterface
    {
        return new FakeRestClient($this);
    }

    /**
     * Record a call to the API with raw endpoint string.
     *
     * @param array<string, mixed> $params
     */
    public function recordCall(string $method, string $endpoint, array $params): void
    {
        $this->calls[] = [
            'method'   => $method,
            'endpoint' => $endpoint,
            'params'   => $params,
        ];
    }

    /**
     * Record a call to an endpoint.
     *
     * @param array<string, mixed> $params
     */
    public function recordEndpointCall(string $method, Endpoint $endpoint, array $params): void
    {
        $this->recordCall($method, $endpoint->path(), $params);
    }

    /**
     * Get the response for an endpoint.
     */
    public function getResponseFor(Endpoint $endpoint): mixed
    {
        return $this->getResponseForPath($endpoint->path()) ?? $endpoint->defaultResponse();
    }

    /**
     * Get the response for a specific endpoint path.
     */
    public function getResponseForPath(string $path): mixed
    {
        return $this->responses[$path] ?? null;
    }

    /**
     * Set a response for a specific endpoint path.
     */
    public function setResponse(string $path, mixed $response): static
    {
        $this->responses[$path] = $response;

        return $this;
    }

    /** @return array<int, array{method: string, endpoint: string, params: array<string, mixed>}> */
    public function getCalls(): array
    {
        return $this->calls;
    }

    public function assertCalled(string $endpoint): void
    {
        $called = $this->hasCalled($endpoint);

        Assert::assertTrue($called, sprintf('Expected endpoint [%s] to be called, but it was not.', $endpoint));
    }

    public function assertNotCalled(string $endpoint): void
    {
        $called = $this->hasCalled($endpoint);

        Assert::assertFalse($called, sprintf('Expected endpoint [%s] not to be called, but it was.', $endpoint));
    }

    /** @param array<string, mixed> $params */
    public function assertCalledWith(string $endpoint, array $params): void
    {
        $found = null;

        foreach ($this->calls as $call) {
            if ($call['endpoint'] === $endpoint && $call['params'] === $params) {
                $found = $call;

                break;
            }
        }

        Assert::assertNotNull(
            $found,
            sprintf('Expected endpoint [%s] to be called with params ', $endpoint) . json_encode($params) . ', but it was not.',
        );
    }

    public function assertCalledWithMethod(string $method, string $endpoint): void
    {
        $found = null;

        foreach ($this->calls as $call) {
            if ($call['method'] === $method && $call['endpoint'] === $endpoint) {
                $found = $call;

                break;
            }
        }

        Assert::assertNotNull(
            $found,
            sprintf('Expected endpoint [%s] to be called with method [%s], but it was not.', $endpoint, $method),
        );
    }

    public function assertCalledTimes(string $endpoint, int $times): void
    {
        $count = $this->getCallCount($endpoint);

        Assert::assertEquals(
            $times,
            $count,
            sprintf('Expected endpoint [%s] to be called %d times, but it was called %d times.', $endpoint, $times, $count),
        );
    }

    public function assertNothingCalled(): void
    {
        Assert::assertEmpty($this->calls, 'Expected no endpoints to be called, but some were.');
    }

    protected function hasCalled(string $endpoint): bool
    {
        foreach ($this->calls as $call) {
            if ($call['endpoint'] === $endpoint) {
                return true;
            }
        }

        return false;
    }

    protected function getCallCount(string $endpoint): int
    {
        $count = 0;

        foreach ($this->calls as $call) {
            if ($call['endpoint'] === $endpoint) {
                $count++;
            }
        }

        return $count;
    }
}
