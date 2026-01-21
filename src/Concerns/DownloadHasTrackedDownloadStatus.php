<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Concerns;

use MartinCamen\ArrCore\Enum\TrackedDownloadStatus;

/** @property string $trackedDownloadStatus */
trait DownloadHasTrackedDownloadStatus
{
    public function hasError(): bool
    {
        return TrackedDownloadStatus::hasError($this->trackedDownloadStatus);
    }
}
