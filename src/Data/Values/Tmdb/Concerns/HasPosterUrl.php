<?php

namespace MartinCamen\ArrCore\Data\Values\Tmdb\Concerns;

use MartinCamen\ArrCore\Data\Values\Tmdb\TmdbValues;

/** @property ?string $posterPath */
trait HasPosterUrl
{
    public function getPosterUrl(?string $size = 'w500'): ?string
    {
        if ($this->posterPath === null) {
            return null;
        }

        return TmdbValues::getImageUrl($this->posterPath, $size);
    }
}
