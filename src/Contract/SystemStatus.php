<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Interface for system status responses across all *arr services.
 *
 * This interface provides a common contract for accessing basic system
 * information regardless of which service (Radarr, Sonarr, Jellyseerr, etc.)
 * the status came from.
 */
interface SystemStatus
{
    /** Get the application/service version */
    public function version(): string;
}
