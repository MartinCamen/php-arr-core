<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Concerns;

use MartinCamen\PhpFileSize\Enums\Unit;
use MartinCamen\PhpFileSize\FileSize;

trait ConvertsFileSize
{
    private function convertToUnit(FileSize $size, Unit $unit, ?int $precision): float
    {
        $fileSize = $size->precision($precision ?? 2);

        return match ($unit) {
            Unit::Byte     => $fileSize->toBytes(),
            Unit::KiloByte => $fileSize->toKilobytes(),
            Unit::MegaByte => $fileSize->toMegabytes(),
            Unit::GigaByte => $fileSize->toGigabytes(),
            Unit::TeraByte => $fileSize->toTerabytes(),
            Unit::PetaByte => $fileSize->toPetabytes(),
        };
    }
}
