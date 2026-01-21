<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\Media;

use MartinCamen\ArrCore\Contract\FromArray;
use MartinCamen\ArrCore\Enum\MediaStatus;
use MartinCamen\ArrCore\Enum\MediaType;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrFileSize;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Progress;
use Override;

final readonly class Series extends Media implements FromArray
{
    public function __construct(
        ArrId $id,
        string $title,
        ?int $year,
        MediaStatus $status,
        bool $monitored,
        Service $source,
        ?ArrFileSize $sizeOnDisk = null,
        ?string $path = null,
        ?string $overview = null,
        ?string $posterUrl = null,
        ?string $fanartUrl = null,
        public ?int $tvdbId = null,
        public ?string $imdbId = null,
        public ?int $tvMazeId = null,
        public ?string $network = null,
        public ?int $runtime = null,
        public ?float $rating = null,
        public ?string $certification = null,
        public int $seasonCount = 0,
        public int $episodeCount = 0,
        public int $episodeFileCount = 0,
        public ?string $seriesType = null,
        public ?string $qualityProfileName = null,
    ) {
        parent::__construct(
            $id,
            MediaType::Series,
            $title,
            $year,
            $status,
            $monitored,
            $source,
            $sizeOnDisk,
            $path,
            $overview,
            $posterUrl,
            $fanartUrl,
        );
    }

    /**
     * Check if all monitored episodes have been downloaded.
     */
    public function hasAllEpisodes(): bool
    {
        if ($this->episodeCount === 0) {
            return false;
        }

        return $this->episodeFileCount >= $this->episodeCount;
    }

    /**
     * Get the percentage of episodes downloaded.
     */
    public function completionProgress(): Progress
    {
        return Progress::fromFraction($this->episodeFileCount, $this->episodeCount);
    }

    /**
     * Get the number of missing episodes.
     */
    public function missingEpisodeCount(): int
    {
        return max(0, $this->episodeCount - $this->episodeFileCount);
    }

    /**
     * Check if series is continuing.
     */
    public function isContinuing(): bool
    {
        return $this->seriesType === 'continuing';
    }

    /**
     * Check if series has ended.
     */
    public function hasEnded(): bool
    {
        return $this->seriesType === 'ended';
    }

    /**
     * Get TVDB URL if available.
     */
    public function tvdbUrl(): ?string
    {
        if ($this->tvdbId === null) {
            return null;
        }

        return "https://thetvdb.com/series/{$this->tvdbId}";
    }

    /**
     * Get IMDb URL if available.
     */
    public function imdbUrl(): ?string
    {
        if ($this->imdbId === null) {
            return null;
        }

        return "https://www.imdb.com/title/{$this->imdbId}/";
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: ArrId::from($data['id']),
            title: (string) $data['title'],
            year: isset($data['year']) ? (int) $data['year'] : null,
            status: MediaStatus::from((string) $data['status']),
            monitored: (bool) ($data['monitored'] ?? true),
            source: Service::from((string) $data['source']),
            sizeOnDisk: isset($data['size_on_disk'])
                ? ArrFileSize::fromBytes((int) $data['size_on_disk'])
                : null,
            path: isset($data['path']) ? (string) $data['path'] : null,
            overview: isset($data['overview']) ? (string) $data['overview'] : null,
            posterUrl: isset($data['poster_url']) ? (string) $data['poster_url'] : null,
            fanartUrl: isset($data['fanart_url']) ? (string) $data['fanart_url'] : null,
            tvdbId: isset($data['tvdb_id']) ? (int) $data['tvdb_id'] : null,
            imdbId: isset($data['imdb_id']) ? (string) $data['imdb_id'] : null,
            tvMazeId: isset($data['tv_maze_id']) ? (int) $data['tv_maze_id'] : null,
            network: isset($data['network']) ? (string) $data['network'] : null,
            runtime: isset($data['runtime']) ? (int) $data['runtime'] : null,
            rating: isset($data['rating']) ? (float) $data['rating'] : null,
            certification: isset($data['certification']) ? (string) $data['certification'] : null,
            seasonCount: (int) ($data['season_count'] ?? 0),
            episodeCount: (int) ($data['episode_count'] ?? 0),
            episodeFileCount: (int) ($data['episode_file_count'] ?? 0),
            seriesType: isset($data['series_type']) ? (string) $data['series_type'] : null,
            qualityProfileName: isset($data['quality_profile_name']) ? (string) $data['quality_profile_name'] : null,
        );
    }

    #[Override]
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'tvdb_id'              => $this->tvdbId,
            'imdb_id'              => $this->imdbId,
            'tv_maze_id'           => $this->tvMazeId,
            'network'              => $this->network,
            'runtime'              => $this->runtime,
            'rating'               => $this->rating,
            'certification'        => $this->certification,
            'season_count'         => $this->seasonCount,
            'episode_count'        => $this->episodeCount,
            'episode_file_count'   => $this->episodeFileCount,
            'series_type'          => $this->seriesType,
            'quality_profile_name' => $this->qualityProfileName,
            'completion_progress'  => $this->completionProgress()->toArray(),
        ]);
    }
}
