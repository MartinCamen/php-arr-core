<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Testing\Factories;

/**
 * Base factory for *arr service system status records.
 *
 * Provides shared structure for Radarr and Sonarr system status factories.
 * Service-specific factories should extend this and override getServiceDefaults().
 */
abstract class ArrSystemStatusFactory
{
    /**
     * Create a system status record.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function make(array $overrides = []): array
    {
        return array_merge(
            static::getSharedDefaults(),
            static::getServiceDefaults(),
            $overrides,
        );
    }

    /**
     * Get shared default attributes common to all *arr services.
     *
     * @return array<string, mixed>
     */
    protected static function getSharedDefaults(): array
    {
        return [
            'buildTime'              => '2024-01-01T00:00:00Z',
            'isDebug'                => false,
            'isProduction'           => true,
            'isAdmin'                => false,
            'isUserInteractive'      => false,
            'appData'                => '/config',
            'osName'                 => 'ubuntu',
            'osVersion'              => '22.04',
            'isNetCore'              => true,
            'isLinux'                => true,
            'isOsx'                  => false,
            'isWindows'              => false,
            'isDocker'               => true,
            'mode'                   => 'console',
            'databaseType'           => 'sqlite3',
            'databaseVersion'        => '3.40.0',
            'authentication'         => 'forms',
            'urlBase'                => '',
            'runtimeVersion'         => '8.0.0',
            'runtimeName'            => '.NET',
            'startTime'              => '2024-01-01T00:00:00Z',
            'packageUpdateMechanism' => 'docker',
        ];
    }

    /**
     * Get service-specific default attributes.
     *
     * Override this in service-specific factories to provide
     * service-specific fields like appName, version, branch, etc.
     *
     * @return array<string, mixed>
     */
    abstract protected static function getServiceDefaults(): array;
}
