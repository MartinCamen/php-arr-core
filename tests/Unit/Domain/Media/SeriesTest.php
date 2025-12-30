<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Domain\Media;

use MartinCamen\ArrCore\Domain\Media\Series;
use MartinCamen\ArrCore\Enum\MediaStatus;
use MartinCamen\ArrCore\Enum\MediaType;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Progress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SeriesTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $series = new Series(
            id: ArrId::fromInt(123),
            title: 'Breaking Bad',
            year: 2008,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Sonarr,
        );

        $this->assertSame(123, $series->id->value());
        $this->assertSame('Breaking Bad', $series->title);
        $this->assertSame(2008, $series->year);
        $this->assertSame(MediaType::Series, $series->type);
        $this->assertSame(MediaStatus::Available, $series->status);
    }

    #[Test]
    public function calculatesCompletionProgress(): void
    {
        $series = new Series(
            id: ArrId::fromInt(1),
            title: 'Test Series',
            year: 2024,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Sonarr,
            episodeCount: 10,
            episodeFileCount: 5,
        );

        $progress = $series->completionProgress();

        $this->assertInstanceOf(Progress::class, $progress);
        $this->assertSame(50.0, $progress->percentage());
    }

    #[Test]
    public function detectsAllEpisodes(): void
    {
        $complete = new Series(
            id: ArrId::fromInt(1),
            title: 'Complete Series',
            year: 2024,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Sonarr,
            episodeCount: 10,
            episodeFileCount: 10,
        );

        $incomplete = new Series(
            id: ArrId::fromInt(2),
            title: 'Incomplete Series',
            year: 2024,
            status: MediaStatus::Missing,
            monitored: true,
            source: Service::Sonarr,
            episodeCount: 10,
            episodeFileCount: 5,
        );

        $noEpisodes = new Series(
            id: ArrId::fromInt(3),
            title: 'No Episodes',
            year: 2024,
            status: MediaStatus::Unknown,
            monitored: true,
            source: Service::Sonarr,
            episodeCount: 0,
            episodeFileCount: 0,
        );

        $this->assertTrue($complete->hasAllEpisodes());
        $this->assertFalse($incomplete->hasAllEpisodes());
        $this->assertFalse($noEpisodes->hasAllEpisodes());
    }

    #[Test]
    public function calculatesMissingEpisodes(): void
    {
        $series = new Series(
            id: ArrId::fromInt(1),
            title: 'Test Series',
            year: 2024,
            status: MediaStatus::Missing,
            monitored: true,
            source: Service::Sonarr,
            episodeCount: 10,
            episodeFileCount: 3,
        );

        $this->assertSame(7, $series->missingEpisodeCount());
    }

    #[Test]
    public function detectsSeriesType(): void
    {
        $continuing = new Series(
            id: ArrId::fromInt(1),
            title: 'Continuing Series',
            year: 2024,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Sonarr,
            seriesType: 'continuing',
        );

        $ended = new Series(
            id: ArrId::fromInt(2),
            title: 'Ended Series',
            year: 2020,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Sonarr,
            seriesType: 'ended',
        );

        $this->assertTrue($continuing->isContinuing());
        $this->assertFalse($continuing->hasEnded());
        $this->assertFalse($ended->isContinuing());
        $this->assertTrue($ended->hasEnded());
    }

    #[Test]
    public function generatesExternalUrls(): void
    {
        $series = new Series(
            id: ArrId::fromInt(1),
            title: 'Breaking Bad',
            year: 2008,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Sonarr,
            tvdbId: 81189,
            imdbId: 'tt0903747',
        );

        $this->assertSame('https://thetvdb.com/series/81189', $series->tvdbUrl());
        $this->assertSame('https://www.imdb.com/title/tt0903747/', $series->imdbUrl());
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $series = Series::fromArray([
            'id'                 => 123,
            'title'              => 'Breaking Bad',
            'year'               => 2008,
            'status'             => 'available',
            'monitored'          => true,
            'source'             => 'sonarr',
            'tvdb_id'            => 81189,
            'episode_count'      => 62,
            'episode_file_count' => 62,
            'series_type'        => 'ended',
        ]);

        $this->assertSame('Breaking Bad', $series->title);
        $this->assertSame(81189, $series->tvdbId);
        $this->assertSame(62, $series->episodeCount);
        $this->assertTrue($series->hasEnded());
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $series = new Series(
            id: ArrId::fromInt(123),
            title: 'Breaking Bad',
            year: 2008,
            status: MediaStatus::Available,
            monitored: true,
            source: Service::Sonarr,
            tvdbId: 81189,
            episodeCount: 62,
            episodeFileCount: 62,
        );

        $array = $series->toArray();

        $this->assertSame('123', $array['id']);
        $this->assertSame('series', $array['type']);
        $this->assertSame('Breaking Bad', $array['title']);
        $this->assertSame(81189, $array['tvdb_id']);
        $this->assertArrayHasKey('completion_progress', $array);
    }
}
