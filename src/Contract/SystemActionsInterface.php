<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Contract;

/**
 * Interface for system actions across all *arr services.
 *
 * Implementing this interface ensures that all service SDKs provide
 * a consistent way to access system status information.
 */
interface SystemActionsInterface
{
    /**
     * Get system status including version info.
     *
     * The return type varies by service but will always implement SystemStatus.
     */
    public function status(): SystemStatus;
}
