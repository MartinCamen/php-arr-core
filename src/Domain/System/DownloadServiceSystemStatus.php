<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\System;

use MartinCamen\ArrCore\Contract\Arrayable;
use MartinCamen\ArrCore\Contract\FromArray;

final readonly class DownloadServiceSystemStatus implements Arrayable, FromArray
{
    public function __construct(
        public string $appName,
        public string $instanceName,
        public string $version,
        public string $buildTime,
        public bool $isDebug,
        public bool $isProduction,
        public bool $isAdmin,
        public bool $isUserInteractive,
        public string $startupPath,
        public string $appData,
        public string $osName,
        public string $osVersion,
        public bool $isNetCore,
        public bool $isLinux,
        public bool $isOsx,
        public bool $isWindows,
        public bool $isDocker,
        public string $mode,
        public string $branch,
        public ?string $databaseType,
        public ?string $databaseVersion,
        public string $authentication,
        public int $migrationVersion,
        public string $urlBase,
        public string $runtimeVersion,
        public string $runtimeName,
        public string $startTime,
        public string $packageVersion,
        public string $packageAuthor,
        public string $packageUpdateMechanism,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        return new self(
            appName: $data['appName'] ?? '',
            instanceName: $data['instanceName'] ?? '',
            version: $data['version'] ?? '',
            buildTime: $data['buildTime'] ?? '',
            isDebug: $data['isDebug'] ?? false,
            isProduction: $data['isProduction'] ?? true,
            isAdmin: $data['isAdmin'] ?? false,
            isUserInteractive: $data['isUserInteractive'] ?? false,
            startupPath: $data['startupPath'] ?? '',
            appData: $data['appData'] ?? '',
            osName: $data['osName'] ?? '',
            osVersion: $data['osVersion'] ?? '',
            isNetCore: $data['isNetCore'] ?? false,
            isLinux: $data['isLinux'] ?? false,
            isOsx: $data['isOsx'] ?? false,
            isWindows: $data['isWindows'] ?? false,
            isDocker: $data['isDocker'] ?? false,
            mode: $data['mode'] ?? '',
            branch: $data['branch'] ?? '',
            databaseType: $data['databaseType'] ?? null,
            databaseVersion: $data['databaseVersion'] ?? null,
            authentication: $data['authentication'] ?? '',
            migrationVersion: $data['migrationVersion'] ?? 0,
            urlBase: $data['urlBase'] ?? '',
            runtimeVersion: $data['runtimeVersion'] ?? '',
            runtimeName: $data['runtimeName'] ?? '',
            startTime: $data['startTime'] ?? '',
            packageVersion: $data['packageVersion'] ?? '',
            packageAuthor: $data['packageAuthor'] ?? '',
            packageUpdateMechanism: $data['packageUpdateMechanism'] ?? '',
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'app_name'                 => $this->appName,
            'instance_name'            => $this->instanceName,
            'version'                  => $this->version,
            'build_time'               => $this->buildTime,
            'is_debug'                 => $this->isDebug,
            'is_production'            => $this->isProduction,
            'is_admin'                 => $this->isAdmin,
            'is_user_interactive'      => $this->isUserInteractive,
            'startup_path'             => $this->startupPath,
            'app_data'                 => $this->appData,
            'os_name'                  => $this->osName,
            'os_version'               => $this->osVersion,
            'is_net_core'              => $this->isNetCore,
            'is_linux'                 => $this->isLinux,
            'is_osx'                   => $this->isOsx,
            'is_windows'               => $this->isWindows,
            'is_docker'                => $this->isDocker,
            'mode'                     => $this->mode,
            'branch'                   => $this->branch,
            'database_type'            => $this->databaseType,
            'database_version'         => $this->databaseVersion,
            'authentication'           => $this->authentication,
            'migration_version'        => $this->migrationVersion,
            'url_base'                 => $this->urlBase,
            'runtime_version'          => $this->runtimeVersion,
            'runtime_name'             => $this->runtimeName,
            'start_time'               => $this->startTime,
            'package_version'          => $this->packageVersion,
            'package_author'           => $this->packageAuthor,
            'package_update_mechanism' => $this->packageUpdateMechanism,
        ];
    }
}
