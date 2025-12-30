<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Domain\Download;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use MartinCamen\ArrCore\Contract\Arrayable;
use MartinCamen\ArrCore\Enum\DownloadStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\ValueObject\FileSize;
use MartinCamen\ArrCore\ValueObject\Progress;
use Traversable;

/**
 * @implements IteratorAggregate<int, DownloadItem>
 */
final readonly class DownloadItemCollection implements Arrayable, Countable, IteratorAggregate
{
    /** @var array<int, DownloadItem> */
    private array $items;

    public function __construct(DownloadItem ...$items)
    {
        $this->items = array_values($items);
    }

    /**
     * @param array<int, array<string, mixed>> $data
     */
    public static function fromArray(array $data): self
    {
        $items = array_map(
            DownloadItem::fromArray(...),
            $data,
        );

        return new self(...$items);
    }

    /**
     * @return array<int, DownloadItem>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function first(): ?DownloadItem
    {
        return $this->items[0] ?? null;
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Traversable<int, DownloadItem>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Filter items by callback.
     *
     * @param callable(DownloadItem): bool $callback
     */
    public function filter(callable $callback): self
    {
        return new self(...array_filter($this->items, $callback));
    }

    /**
     * Filter items by status.
     */
    public function byStatus(DownloadStatus $status): self
    {
        return $this->filter(fn(DownloadItem $item): bool => $item->status === $status);
    }

    /**
     * Filter items by source service.
     */
    public function bySource(Service $source): self
    {
        return $this->filter(fn(DownloadItem $item): bool => $item->source === $source);
    }

    /**
     * Get only active downloads.
     */
    public function active(): self
    {
        return $this->filter(fn(DownloadItem $item): bool => $item->isActive());
    }

    /**
     * Get only completed downloads.
     */
    public function completed(): self
    {
        return $this->byStatus(DownloadStatus::Completed);
    }

    /**
     * Get only failed downloads.
     */
    public function failed(): self
    {
        return $this->byStatus(DownloadStatus::Failed);
    }

    /**
     * Get only waiting downloads.
     */
    public function waiting(): self
    {
        return $this->filter(fn(DownloadItem $item): bool => $item->isWaiting());
    }

    /**
     * Get downloads with errors.
     */
    public function withErrors(): self
    {
        return $this->filter(fn(DownloadItem $item): bool => $item->hasError());
    }

    /**
     * Sort items by priority (errors first, then active, then waiting).
     */
    public function sortByPriority(): self
    {
        $items = $this->items;

        usort($items, fn(DownloadItem $a, DownloadItem $b): int => $a->status->priority() <=> $b->status->priority());

        return new self(...$items);
    }

    /**
     * Get total size of all downloads.
     */
    public function totalSize(): FileSize
    {
        return array_reduce(
            $this->items,
            static fn(FileSize $total, DownloadItem $item): FileSize => $total->add($item->size),
            FileSize::zero(),
        );
    }

    /**
     * Get total remaining size.
     */
    public function totalRemaining(): FileSize
    {
        return array_reduce(
            $this->items,
            static fn(FileSize $total, DownloadItem $item): FileSize => $total->add($item->sizeRemaining),
            FileSize::zero(),
        );
    }

    /**
     * Get overall progress across all downloads.
     */
    public function totalProgress(): Progress
    {
        $totalBytes = $this->totalSize()->bytes();

        if ($totalBytes === 0) {
            return Progress::zero();
        }

        $downloadedBytes = $totalBytes - $this->totalRemaining()->bytes();

        return Progress::fromFraction($downloadedBytes, $totalBytes);
    }

    /**
     * Merge with another collection.
     */
    public function merge(self $other): self
    {
        return new self(...$this->items, ...$other->items);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(
                static fn(DownloadItem $item): array => $item->toArray(),
                $this->items,
            ),
            'count'           => $this->count(),
            'total_size'      => $this->totalSize()->toArray(),
            'total_remaining' => $this->totalRemaining()->toArray(),
            'total_progress'  => $this->totalProgress()->toArray(),
        ];
    }
}
