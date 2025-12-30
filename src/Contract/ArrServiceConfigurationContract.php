<?php

namespace MartinCamen\ArrCore\Contract;

use MartinCamen\ArrCore\Config\ArrServiceConfiguration;

/**
 * @property string $host
 * @property int $prot
 * @property string $apiKey
 * @property bool $useHttps
 * @property int $timeout
 * @property string $urlBase
 * @property string|null $apiVersion
 *
 * @phpstan-require-extends ArrServiceConfiguration
 */
interface ArrServiceConfigurationContract
{
    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self;

    /** @return array<string, mixed> */
    public static function toArray(): array;

    public static function getBaseUrl(): string;
}
