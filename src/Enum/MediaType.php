<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Enum;

enum MediaType: string
{
    case Movie = 'movie';
    case Series = 'series';
    case Season = 'season';
    case Episode = 'episode';
    case Artist = 'artist';
    case Album = 'album';
    case Track = 'track';
    case Book = 'book';
    case Author = 'author';

    /**
     * Check if type represents video content.
     */
    public function isVideo(): bool
    {
        return match ($this) {
            self::Movie, self::Series, self::Season, self::Episode => true,
            default => false,
        };
    }

    /**
     * Check if type represents audio content.
     */
    public function isAudio(): bool
    {
        return match ($this) {
            self::Artist, self::Album, self::Track => true,
            default => false,
        };
    }

    /**
     * Check if type represents written content.
     */
    public function isWritten(): bool
    {
        return match ($this) {
            self::Book, self::Author => true,
            default => false,
        };
    }

    /**
     * Check if type is a container (can have children).
     */
    public function isContainer(): bool
    {
        return match ($this) {
            self::Series, self::Season, self::Artist, self::Album, self::Author => true,
            default => false,
        };
    }

    /**
     * Get human-readable label (singular).
     */
    public function label(): string
    {
        return match ($this) {
            self::Movie   => 'Movie',
            self::Series  => 'Series',
            self::Season  => 'Season',
            self::Episode => 'Episode',
            self::Artist  => 'Artist',
            self::Album   => 'Album',
            self::Track   => 'Track',
            self::Book    => 'Book',
            self::Author  => 'Author',
        };
    }

    /**
     * Get human-readable label (plural).
     */
    public function labelPlural(): string
    {
        return match ($this) {
            self::Movie   => 'Movies',
            self::Series  => 'Series',
            self::Season  => 'Seasons',
            self::Episode => 'Episodes',
            self::Artist  => 'Artists',
            self::Album   => 'Albums',
            self::Track   => 'Tracks',
            self::Book    => 'Books',
            self::Author  => 'Authors',
        };
    }
}
