<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\Media;

use MartinCamen\ArrCore\Contract\Arrayable;
use MartinCamen\ArrCore\Enum\MediaStatus;
use MartinCamen\ArrCore\Enum\MediaType;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\FileSize;

abstract readonly class Media implements Arrayable
{
    public function __construct(
        public ArrId $id,
        public MediaType $type,
        public string $title,
        public ?int $year,
        public MediaStatus $status,
        public bool $monitored,
        public Service $source,
        public ?FileSize $sizeOnDisk = null,
        public ?string $path = null,
        public ?string $overview = null,
        public ?string $posterUrl = null,
        public ?string $fanartUrl = null,
    ) {}

    /**
     * Check if media has files on disk.
     */
    public function hasFiles(): bool
    {
        return $this->sizeOnDisk instanceof FileSize && ! $this->sizeOnDisk->isZero();
    }

    /**
     * Check if media is complete (available and has files).
     */
    public function isComplete(): bool
    {
        return $this->status->hasMedia() && $this->hasFiles();
    }

    /**
     * Check if media needs attention (missing, failed, etc).
     */
    public function needsAttention(): bool
    {
        return $this->monitored && $this->status->needsAttention();
    }

    /**
     * Get a display-friendly title with year.
     */
    public function displayTitle(): string
    {
        if ($this->year !== null) {
            return "{$this->title} ({$this->year})";
        }

        return $this->title;
    }

    public function toArray(): array
    {
        return [
            'id'           => (string) $this->id,
            'type'         => $this->type->value,
            'title'        => $this->title,
            'year'         => $this->year,
            'status'       => $this->status->value,
            'monitored'    => $this->monitored,
            'source'       => $this->source->value,
            'size_on_disk' => $this->sizeOnDisk?->toArray(),
            'path'         => $this->path,
            'overview'     => $this->overview,
            'poster_url'   => $this->posterUrl,
            'fanart_url'   => $this->fanartUrl,
        ];
    }
}
