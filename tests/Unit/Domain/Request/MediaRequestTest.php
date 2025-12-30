<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Domain\Request;

use MartinCamen\ArrCore\Domain\Request\MediaRequest;
use MartinCamen\ArrCore\Enum\MediaType;
use MartinCamen\ArrCore\Enum\RequestStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Timestamp;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MediaRequestTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $request = new MediaRequest(
            id: ArrId::fromInt(123),
            mediaType: MediaType::Movie,
            title: 'The Matrix',
            year: 1999,
            status: RequestStatus::Pending,
            source: Service::Jellyseerr,
        );

        $this->assertSame(123, $request->id->value());
        $this->assertSame(MediaType::Movie, $request->mediaType);
        $this->assertSame('The Matrix', $request->title);
        $this->assertSame(1999, $request->year);
        $this->assertSame(RequestStatus::Pending, $request->status);
        $this->assertSame(Service::Jellyseerr, $request->source);
    }

    #[Test]
    public function detectsPendingRequests(): void
    {
        $pending = new MediaRequest(
            id: ArrId::fromInt(1),
            mediaType: MediaType::Movie,
            title: 'Pending Movie',
            year: 2024,
            status: RequestStatus::Pending,
            source: Service::Jellyseerr,
        );

        $approved = new MediaRequest(
            id: ArrId::fromInt(2),
            mediaType: MediaType::Movie,
            title: 'Approved Movie',
            year: 2024,
            status: RequestStatus::Approved,
            source: Service::Jellyseerr,
        );

        $this->assertTrue($pending->isPending());
        $this->assertFalse($approved->isPending());
    }

    #[Test]
    public function detectsApprovedRequests(): void
    {
        $approved = new MediaRequest(
            id: ArrId::fromInt(1),
            mediaType: MediaType::Movie,
            title: 'Approved Movie',
            year: 2024,
            status: RequestStatus::Approved,
            source: Service::Jellyseerr,
        );

        $pending = new MediaRequest(
            id: ArrId::fromInt(2),
            mediaType: MediaType::Movie,
            title: 'Pending Movie',
            year: 2024,
            status: RequestStatus::Pending,
            source: Service::Jellyseerr,
        );

        $this->assertTrue($approved->isApproved());
        $this->assertFalse($pending->isApproved());
    }

    #[Test]
    public function detectsFulfilledRequests(): void
    {
        $fulfilled = new MediaRequest(
            id: ArrId::fromInt(1),
            mediaType: MediaType::Movie,
            title: 'Fulfilled Movie',
            year: 2024,
            status: RequestStatus::Fulfilled,
            source: Service::Jellyseerr,
        );

        $approved = new MediaRequest(
            id: ArrId::fromInt(2),
            mediaType: MediaType::Movie,
            title: 'Approved Movie',
            year: 2024,
            status: RequestStatus::Approved,
            source: Service::Jellyseerr,
        );

        $this->assertTrue($fulfilled->isFulfilled());
        $this->assertFalse($approved->isFulfilled());
    }

    #[Test]
    public function detectsRequestsNeedingAction(): void
    {
        $pending = new MediaRequest(
            id: ArrId::fromInt(1),
            mediaType: MediaType::Movie,
            title: 'Pending Movie',
            year: 2024,
            status: RequestStatus::Pending,
            source: Service::Jellyseerr,
        );

        $fulfilled = new MediaRequest(
            id: ArrId::fromInt(2),
            mediaType: MediaType::Movie,
            title: 'Fulfilled Movie',
            year: 2024,
            status: RequestStatus::Fulfilled,
            source: Service::Jellyseerr,
        );

        $this->assertTrue($pending->needsAction());
        $this->assertFalse($fulfilled->needsAction());
    }

    #[Test]
    public function displaysTitle(): void
    {
        $withYear = new MediaRequest(
            id: ArrId::fromInt(1),
            mediaType: MediaType::Movie,
            title: 'The Matrix',
            year: 1999,
            status: RequestStatus::Pending,
            source: Service::Jellyseerr,
        );

        $withoutYear = new MediaRequest(
            id: ArrId::fromInt(2),
            mediaType: MediaType::Movie,
            title: 'Unknown Movie',
            year: null,
            status: RequestStatus::Pending,
            source: Service::Jellyseerr,
        );

        $this->assertSame('The Matrix (1999)', $withYear->displayTitle());
        $this->assertSame('Unknown Movie', $withoutYear->displayTitle());
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $request = MediaRequest::fromArray([
            'id'           => 123,
            'media_type'   => MediaType::Movie->value,
            'title'        => 'The Matrix',
            'year'         => 1999,
            'status'       => 'pending',
            'source'       => 'jellyseerr',
            'media_id'     => 456,
            'external_id'  => 603,
            'requested_by' => 'john_doe',
            'requested_at' => '2024-01-15T10:30:00Z',
            'poster_url'   => 'https://example.com/poster.jpg',
        ]);

        $this->assertSame('The Matrix', $request->title);
        $this->assertSame(MediaType::Movie, $request->mediaType);
        $this->assertSame(RequestStatus::Pending, $request->status);
        $this->assertSame(456, $request->mediaId?->value());
        $this->assertSame(603, $request->externalId);
        $this->assertSame('john_doe', $request->requestedBy);
        $this->assertInstanceOf(Timestamp::class, $request->requestedAt);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $request = new MediaRequest(
            id: ArrId::fromInt(123),
            mediaType: MediaType::Movie,
            title: 'The Matrix',
            year: 1999,
            status: RequestStatus::Approved,
            source: Service::Jellyseerr,
            mediaId: ArrId::fromInt(456),
            externalId: 603,
            requestedBy: 'john_doe',
        );

        $array = $request->toArray();

        $this->assertSame('123', $array['id']);
        $this->assertSame('movie', $array['media_type']);
        $this->assertSame('The Matrix', $array['title']);
        $this->assertSame(1999, $array['year']);
        $this->assertSame('approved', $array['status']);
        $this->assertSame('jellyseerr', $array['source']);
        $this->assertSame('456', $array['media_id']);
        $this->assertSame(603, $array['external_id']);
        $this->assertSame('john_doe', $array['requested_by']);
    }

    #[Test]
    public function supportsSeriesRequests(): void
    {
        $request = new MediaRequest(
            id: ArrId::fromInt(1),
            mediaType: MediaType::Series,
            title: 'Breaking Bad',
            year: 2008,
            status: RequestStatus::Pending,
            source: Service::Jellyseerr,
        );

        $this->assertSame(MediaType::Series, $request->mediaType);
        $this->assertSame('Breaking Bad (2008)', $request->displayTitle());
    }
}
