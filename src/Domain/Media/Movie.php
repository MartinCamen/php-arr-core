<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\Media;

use MartinCamen\ArrCore\Contract\FromArray;
use MartinCamen\ArrCore\Enum\MediaStatus;
use MartinCamen\ArrCore\Enum\MediaType;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrFileSize;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Duration;
use Override;

final readonly class Movie extends Media implements FromArray
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
        public ?string $imdbId = null,
        public ?int $tmdbId = null,
        public ?Duration $runtime = null,
        public ?string $studio = null,
        public ?float $rating = null,
        public ?string $certification = null,
        public bool $hasFile = false,
        public ?string $qualityProfileName = null,
    ) {
        parent::__construct(
            $id,
            MediaType::Movie,
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
     * Check if movie is released.
     */
    public function isReleased(): bool
    {
        return match ($this->status) {
            MediaStatus::Available, MediaStatus::Downloaded, MediaStatus::Missing => true,
            default => false,
        };
    }

    /**
     * Check if movie is downloadable (released and missing).
     */
    public function isDownloadable(): bool
    {
        return $this->monitored
            && $this->isReleased()
            && ! $this->hasFile;
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
     * Get TMDb URL if available.
     */
    public function tmdbUrl(): ?string
    {
        if ($this->tmdbId === null) {
            return null;
        }

        return "https://www.themoviedb.org/movie/{$this->tmdbId}";
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
            imdbId: isset($data['imdb_id']) ? (string) $data['imdb_id'] : null,
            tmdbId: isset($data['tmdb_id']) ? (int) $data['tmdb_id'] : null,
            runtime: isset($data['runtime'])
                ? Duration::fromMinutes((int) $data['runtime'])
                : null,
            studio: isset($data['studio']) ? (string) $data['studio'] : null,
            rating: isset($data['rating']) ? (float) $data['rating'] : null,
            certification: isset($data['certification']) ? (string) $data['certification'] : null,
            hasFile: (bool) ($data['has_file'] ?? false),
            qualityProfileName: isset($data['quality_profile_name']) ? (string) $data['quality_profile_name'] : null,
        );
    }

    #[Override]
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'imdb_id'              => $this->imdbId,
            'tmdb_id'              => $this->tmdbId,
            'runtime'              => $this->runtime?->toArray(),
            'studio'               => $this->studio,
            'rating'               => $this->rating,
            'certification'        => $this->certification,
            'has_file'             => $this->hasFile,
            'quality_profile_name' => $this->qualityProfileName,
        ]);
    }
}
