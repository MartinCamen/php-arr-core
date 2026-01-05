<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\Download;

use MartinCamen\ArrCore\Contract\Arrayable;
use MartinCamen\ArrCore\Contract\FromArray;
use MartinCamen\ArrCore\Enum\DownloadStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\ArrFileSize;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\Progress;
use MartinCamen\ArrCore\ValueObject\Timestamp;

final readonly class DownloadItem implements Arrayable, FromArray
{
    public function __construct(
        public ArrId $id,
        public string $name,
        public ArrFileSize $size,
        public ArrFileSize $sizeRemaining,
        public Progress $progress,
        public DownloadStatus $status,
        public Service $source,
        public ?Duration $eta = null,
        public ?string $downloadClient = null,
        public ?string $indexer = null,
        public ?string $category = null,
        public ?string $outputPath = null,
        public ?ArrId $mediaId = null,
        public ?string $mediaTitle = null,
        public ?string $errorMessage = null,
        public ?Timestamp $addedAt = null,
        public ?int $priority = null,
    ) {}

    /**
     * Check if download is actively progressing.
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Check if download is waiting.
     */
    public function isWaiting(): bool
    {
        return $this->status->isWaiting();
    }

    /**
     * Check if download has an error.
     */
    public function hasError(): bool
    {
        return $this->status->isError() || $this->errorMessage !== null;
    }

    /**
     * Check if download is complete.
     */
    public function isComplete(): bool
    {
        return $this->status === DownloadStatus::Completed || $this->progress->isComplete();
    }

    /**
     * Get downloaded size.
     */
    public function downloadedSize(): ArrFileSize
    {
        return ArrFileSize::fromBytes(
            $this->size->getBytes() - $this->sizeRemaining->getBytes(),
        );
    }

    /**
     * Estimate download speed in MB/s.
     */
    public function speedMbps(): ?float
    {
        if (! $this->eta instanceof Duration || $this->eta->isZero()) {
            return null;
        }

        $remainingMb = $this->sizeRemaining->toMegabytes();
        $secondsLeft = $this->eta->seconds();

        if ($secondsLeft <= 0) {
            return null;
        }

        return $remainingMb / $secondsLeft;
    }

    /**
     * Get a display-friendly title.
     */
    public function displayTitle(): string
    {
        return $this->mediaTitle ?? $this->name;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: ArrId::from($data['id']),
            name: (string) $data['name'],
            size: ArrFileSize::fromBytes((int) ($data['size'] ?? 0)),
            sizeRemaining: ArrFileSize::fromBytes((int) ($data['size_remaining'] ?? 0)),
            progress: Progress::fromPercentage((float) ($data['progress'] ?? 0)),
            status: DownloadStatus::from((string) $data['status']),
            source: Service::from((string) $data['source']),
            eta: isset($data['eta']) ? Duration::fromSeconds((int) $data['eta']) : null,
            downloadClient: isset($data['download_client']) ? (string) $data['download_client'] : null,
            indexer: isset($data['indexer']) ? (string) $data['indexer'] : null,
            category: isset($data['category']) ? (string) $data['category'] : null,
            outputPath: isset($data['output_path']) ? (string) $data['output_path'] : null,
            mediaId: isset($data['media_id']) ? ArrId::from($data['media_id']) : null,
            mediaTitle: isset($data['media_title']) ? (string) $data['media_title'] : null,
            errorMessage: isset($data['error_message']) ? (string) $data['error_message'] : null,
            addedAt: isset($data['added_at']) ? Timestamp::fromString((string) $data['added_at']) : null,
            priority: isset($data['priority']) ? (int) $data['priority'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'              => (string) $this->id,
            'name'            => $this->name,
            'size'            => $this->size->toArray(),
            'size_remaining'  => $this->sizeRemaining->toArray(),
            'progress'        => $this->progress->toArray(),
            'status'          => $this->status->value,
            'source'          => $this->source->value,
            'eta'             => $this->eta?->toArray(),
            'download_client' => $this->downloadClient,
            'indexer'         => $this->indexer,
            'category'        => $this->category,
            'output_path'     => $this->outputPath,
            'media_id'        => $this->mediaId instanceof ArrId ? (string) $this->mediaId : null,
            'media_title'     => $this->mediaTitle,
            'error_message'   => $this->errorMessage,
            'added_at'        => $this->addedAt?->toArray(),
            'priority'        => $this->priority,
        ];
    }
}
