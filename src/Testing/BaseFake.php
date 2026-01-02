<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing;

use MartinCamen\ArrCore\Testing\Traits\HandlesFakeAssertions;
use MartinCamen\ArrCore\Testing\Traits\HandlesFakeCalls;
use MartinCamen\ArrCore\Testing\Traits\HandlesFakeResponses;

class BaseFake
{
    use HandlesFakeAssertions,
        HandlesFakeCalls,
        HandlesFakeResponses;

    /** @var array<string, array<int, array<string, mixed>>> */
    protected array $calls = [];

    /** @param array<string, mixed> $responses */
    public function __construct(protected array $responses = []) {}
}
