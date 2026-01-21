<?php

namespace MartinCamen\ArrCore\Data\Values\Tmdb;

class TmdbValues
{
    private const string BASE_IMAGE_URL = 'https://image.tmdb.org/t/p/%s/%s';

    public static function getImageUrl(
        string $path,
        ?string $size = null,
    ): ?string {
        $size ??= 'original';

        $path = ltrim($path, '/');
        $size = ltrim($size, '/');

        return sprintf(self::BASE_IMAGE_URL, $size, $path);
    }
}
