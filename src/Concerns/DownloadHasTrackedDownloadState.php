<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Concerns;

use MartinCamen\ArrCore\Enum\TrackedDownloadState;

/** @property string $trackedDownloadState */
trait DownloadHasTrackedDownloadState
{
    public function isCompleted(): bool
    {
        return TrackedDownloadState::isCompleted($this->trackedDownloadState);
    }
}
