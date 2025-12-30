<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Domain\Media;

use MartinCamen\ArrCore\Domain\Media\Movie;
use MartinCamen\ArrCore\Enum\MediaStatus;
use MartinCamen\ArrCore\Enum\MediaType;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\FileSize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MovieTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $movie = new Movie(
            id: ArrId::fromInt(123),
            title: 'The Matrix',
            year: 1999,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Radarr,
        );

        $this->assertSame(123, $movie->id->value());
        $this->assertSame('The Matrix', $movie->title);
        $this->assertSame(1999, $movie->year);
        $this->assertSame(MediaType::Movie, $movie->type);
        $this->assertSame(MediaStatus::Available, $movie->status);
        $this->assertTrue($movie->monitored);
        $this->assertSame(Service::Radarr, $movie->source);
    }

    #[Test]
    public function displaysTitle(): void
    {
        $withYear = new Movie(
            id: ArrId::fromInt(1),
            title: 'The Matrix',
            year: 1999,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Radarr,
        );

        $withoutYear = new Movie(
            id: ArrId::fromInt(2),
            title: 'Unknown Movie',
            year: null,
            status: MediaStatus::Unknown,
            monitored: false,
            source: Service::Radarr,
        );

        $this->assertSame('The Matrix (1999)', $withYear->displayTitle());
        $this->assertSame('Unknown Movie', $withoutYear->displayTitle());
    }

    #[Test]
    public function detectsReleasedStatus(): void
    {
        $released = new Movie(
            id: ArrId::fromInt(1),
            title: 'Released Movie',
            year: 2024,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Radarr,
        );

        $announced = new Movie(
            id: ArrId::fromInt(2),
            title: 'Upcoming Movie',
            year: 2025,
            status: MediaStatus::Announced,
            monitored: true,
            source: Service::Radarr,
        );

        $this->assertTrue($released->isReleased());
        $this->assertFalse($announced->isReleased());
    }

    #[Test]
    public function detectsDownloadability(): void
    {
        $downloadable = new Movie(
            id: ArrId::fromInt(1),
            title: 'Missing Movie',
            year: 2024,
            status: MediaStatus::Missing,
            monitored: true,
            source: Service::Radarr,
            hasFile: false,
        );

        $hasFile = new Movie(
            id: ArrId::fromInt(2),
            title: 'Downloaded Movie',
            year: 2024,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Radarr,
            hasFile: true,
        );

        $unmonitored = new Movie(
            id: ArrId::fromInt(3),
            title: 'Unmonitored Movie',
            year: 2024,
            status: MediaStatus::Missing,
            monitored: false,
            source: Service::Radarr,
            hasFile: false,
        );

        $this->assertTrue($downloadable->isDownloadable());
        $this->assertFalse($hasFile->isDownloadable());
        $this->assertFalse($unmonitored->isDownloadable());
    }

    #[Test]
    public function generatesExternalUrls(): void
    {
        $movie = new Movie(
            id: ArrId::fromInt(1),
            title: 'The Matrix',
            year: 1999,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Radarr,
            imdbId: 'tt0133093',
            tmdbId: 603,
        );

        $this->assertSame('https://www.imdb.com/title/tt0133093/', $movie->imdbUrl());
        $this->assertSame('https://www.themoviedb.org/movie/603', $movie->tmdbUrl());
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $movie = Movie::fromArray([
            'id'        => 123,
            'title'     => 'The Matrix',
            'year'      => 1999,
            'status'    => 'available',
            'monitored' => true,
            'source'    => 'radarr',
            'imdb_id'   => 'tt0133093',
            'tmdb_id'   => 603,
            'runtime'   => 136,
        ]);

        $this->assertSame('The Matrix', $movie->title);
        $this->assertSame(MediaStatus::Available, $movie->status);
        $this->assertSame('tt0133093', $movie->imdbId);
        $this->assertInstanceOf(Duration::class, $movie->runtime);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $movie = new Movie(
            id: ArrId::fromInt(123),
            title: 'The Matrix',
            year: 1999,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Radarr,
            sizeOnDisk: FileSize::fromGB(8.5),
            imdbId: 'tt0133093',
        );

        $array = $movie->toArray();

        $this->assertSame('123', $array['id']);
        $this->assertSame('movie', $array['type']);
        $this->assertSame('The Matrix', $array['title']);
        $this->assertSame('available', $array['status']);
        $this->assertSame('radarr', $array['source']);
        $this->assertSame('tt0133093', $array['imdb_id']);
        $this->assertArrayHasKey('size_on_disk', $array);
    }

    #[Test]
    public function detectsHasFiles(): void
    {
        $withFiles = new Movie(
            id: ArrId::fromInt(1),
            title: 'Movie With Files',
            year: 2024,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Radarr,
            sizeOnDisk: FileSize::fromGB(4.5),
        );

        $withoutFiles = new Movie(
            id: ArrId::fromInt(2),
            title: 'Movie Without Files',
            year: 2024,
            status: MediaStatus::Missing,
            monitored: true,
            source: Service::Radarr,
            sizeOnDisk: FileSize::zero(),
        );

        $this->assertTrue($withFiles->hasFiles());
        $this->assertFalse($withoutFiles->hasFiles());
    }
}
