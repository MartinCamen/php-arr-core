<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing;

use MartinCamen\ArrCore\Testing\Traits\AssertsFake;
use MartinCamen\ArrCore\Testing\Traits\HandlesFakeCalls;
use MartinCamen\ArrCore\Testing\Traits\HandlesFakeResponses;

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
