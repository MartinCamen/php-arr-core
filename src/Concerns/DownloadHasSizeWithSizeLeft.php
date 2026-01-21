<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Concerns;

use MartinCamen\ArrCore\ValueObject\ArrFileSize;

/**
 * @property int|float $size
 * @property int|float $sizeLeft
 */
trait DownloadHasSizeWithSizeLeft
{
    public function getProgress(): float
    {
        if ($this->size === 0.0) {
            return 0.0;
        }

        return round((($this->size - $this->sizeLeft) / $this->size) * 100, 2);
    }

    public function getSizeGb(): float
    {
        return ArrFileSize::fromBytes($this->size)->toGigabytes(precision: 2);
    }

    public function getSizeLeftGb(): float
    {
        return ArrFileSize::fromBytes($this->sizeLeft)->toGigabytes(precision: 2);
    }
}
