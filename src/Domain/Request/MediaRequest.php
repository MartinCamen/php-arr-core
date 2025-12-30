<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\Request;

use MartinCamen\ArrCore\Contract\Arrayable;
use MartinCamen\ArrCore\Contract\FromArray;
use MartinCamen\ArrCore\Enum\MediaType;
use MartinCamen\ArrCore\Enum\RequestStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Timestamp;

final readonly class MediaRequest implements Arrayable, FromArray
{
    public function __construct(
        public ArrId $id,
        public MediaType $mediaType,
        public string $title,
        public ?int $year,
        public RequestStatus $status,
        public Service $source,
        public ?ArrId $mediaId = null,
        public ?int $externalId = null,
        public ?string $requestedBy = null,
        public ?Timestamp $requestedAt = null,
        public ?Timestamp $updatedAt = null,
        public ?string $posterUrl = null,
    ) {}

    /**
     * Check if request is pending.
     */
    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    /**
     * Check if request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === RequestStatus::Approved;
    }

    /**
     * Check if request is fulfilled.
     */
    public function isFulfilled(): bool
    {
        return $this->status === RequestStatus::Fulfilled;
    }

    /**
     * Check if request needs action.
     */
    public function needsAction(): bool
    {
        return $this->status->needsAction();
    }

    /**
     * Get display title with year.
     */
    public function displayTitle(): string
    {
        if ($this->year !== null) {
            return "{$this->title} ({$this->year})";
        }

        return $this->title;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: ArrId::from($data['id']),
            mediaType: MediaType::from((string) $data['media_type']),
            title: (string) $data['title'],
            year: isset($data['year']) ? (int) $data['year'] : null,
            status: RequestStatus::from((string) $data['status']),
            source: Service::from((string) $data['source']),
            mediaId: isset($data['media_id']) ? ArrId::from($data['media_id']) : null,
            externalId: isset($data['external_id']) ? (int) $data['external_id'] : null,
            requestedBy: isset($data['requested_by']) ? (string) $data['requested_by'] : null,
            requestedAt: isset($data['requested_at']) ? Timestamp::fromString((string) $data['requested_at']) : null,
            updatedAt: isset($data['updated_at']) ? Timestamp::fromString((string) $data['updated_at']) : null,
            posterUrl: isset($data['poster_url']) ? (string) $data['poster_url'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'           => (string) $this->id,
            'media_type'   => $this->mediaType->value,
            'title'        => $this->title,
            'year'         => $this->year,
            'status'       => $this->status->value,
            'source'       => $this->source->value,
            'media_id'     => $this->mediaId instanceof ArrId ? (string) $this->mediaId : null,
            'external_id'  => $this->externalId,
            'requested_by' => $this->requestedBy,
            'requested_at' => $this->requestedAt?->toArray(),
            'updated_at'   => $this->updatedAt?->toArray(),
            'poster_url'   => $this->posterUrl,
        ];
    }
}
