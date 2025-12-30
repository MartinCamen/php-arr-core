<?php

namespace MartinCamen\ArrCore\Testing;

use PHPUnit\Framework\Assert;

trait AssertsFake
{
    /**
     * Assert a method was called.
     */
    public function assertCalled(string $method): void
    {
        Assert::assertArrayHasKey(
            $method,
            $this->calls,
            sprintf('Expected method [%s] to be called, but it was not.', $method),
        );
    }

    /**
     * Assert a method was not called.
     */
    public function assertNotCalled(string $method): void
    {
        Assert::assertArrayNotHasKey(
            $method,
            $this->calls,
            sprintf('Expected method [%s] not to be called, but it was.', $method),
        );
    }

    /**
     * Assert a method was called with specific parameters.
     *
     * @param array<string, mixed> $params
     */
    public function assertCalledWith(string $method, array $params): void
    {
        $this->assertCalled($method);

        $found = false;

        foreach ($this->calls[$method] as $call) {
            if ($call['params'] === $params) {
                $found = true;

                break;
            }
        }

        Assert::assertTrue(
            $found,
            sprintf(
                'Expected method [%s] to be called with params %s, but it was not.',
                $method,
                json_encode($params),
            ),
        );
    }

    /**
     * Assert a method was called a specific number of times.
     */
    public function assertCalledTimes(string $method, int $times): void
    {
        $actualTimes = isset($this->calls[$method]) ? count($this->calls[$method]) : 0;

        Assert::assertEquals(
            $times,
            $actualTimes,
            sprintf(
                'Expected method [%s] to be called %d times, but it was called %d times.',
                $method,
                $times,
                $actualTimes,
            ),
        );
    }

    /**
     * Assert no methods were called.
     */
    public function assertNothingCalled(): void
    {
        Assert::assertEmpty(
            $this->calls,
            'Expected no methods to be called, but some were.',
        );
    }
}
