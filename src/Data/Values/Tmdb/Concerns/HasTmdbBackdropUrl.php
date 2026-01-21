<?php

namespace MartinCamen\ArrCore\Data\Values\Tmdb\Concerns;

use MartinCamen\ArrCore\Data\Values\Tmdb\TmdbValues;

/** @property ?string $backdropPath */
trait HasTmdbBackdropUrl
{
    public function getTmdbBackdropUrl(?string $size = 'original'): ?string
    {
        if ($this->backdropPath === null) {
            return null;
        }

        return TmdbValues::getImageUrl($this->backdropPath, $size);
    }

    public function getBackdropUrl(?string $size = 'original'): ?string
    {
        return $this->getTmdbBackdropUrl($size);
    }
}
