<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing;

class BaseFake
{
    use AssertsFake,
        HandlesFakeCalls,
        HandlesFakeResponses;

    /** @var array<string, array<int, array<string, mixed>>> */
    private array $calls = [];

    /** @param array<string, mixed> $responses */
    public function __construct(private array $responses = []) {}
}
