<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Enums;

/**
 * Sort direction for paginated API requests.
 *
 * This is a shared enum used by both Radarr and Sonarr.
 */
enum SortDirection: string
{
    case Ascending = 'ascending';
    case Descending = 'descending';
}
