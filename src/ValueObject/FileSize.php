<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\ValueObject;

use InvalidArgumentException;
use MartinCamen\ArrCore\Contract\Arrayable;

final readonly class FileSize implements Arrayable
{
    private const int BYTES_PER_KB = 1024;
    private const int BYTES_PER_MB = 1024 * 1024;
    private const int BYTES_PER_GB = 1024 * 1024 * 1024;
    private const int BYTES_PER_TB = 1024 * 1024 * 1024 * 1024;

    private function __construct(private int $bytes)
    {
        if ($this->bytes < 0) {
            throw new InvalidArgumentException('FileSize cannot be negative');
        }
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function fromBytes(int $bytes): self
    {
        return new self($bytes);
    }

    public static function fromKB(float $kb): self
    {
        return new self((int) round($kb * self::BYTES_PER_KB));
    }

    public static function fromMB(float $mb): self
    {
        return new self((int) round($mb * self::BYTES_PER_MB));
    }

    public static function fromGB(float $gb): self
    {
        return new self((int) round($gb * self::BYTES_PER_GB));
    }

    public static function fromTB(float $tb): self
    {
        return new self((int) round($tb * self::BYTES_PER_TB));
    }

    public function bytes(): int
    {
        return $this->bytes;
    }

    public function kb(): float
    {
        return $this->bytes / self::BYTES_PER_KB;
    }

    public function mb(): float
    {
        return $this->bytes / self::BYTES_PER_MB;
    }

    public function gb(): float
    {
        return $this->bytes / self::BYTES_PER_GB;
    }

    public function tb(): float
    {
        return $this->bytes / self::BYTES_PER_TB;
    }

    public function formatted(int $precision = 2): string
    {
        if ($this->bytes >= self::BYTES_PER_TB) {
            return round($this->tb(), $precision) . ' TB';
        }

        if ($this->bytes >= self::BYTES_PER_GB) {
            return round($this->gb(), $precision) . ' GB';
        }

        if ($this->bytes >= self::BYTES_PER_MB) {
            return round($this->mb(), $precision) . ' MB';
        }

        if ($this->bytes >= self::BYTES_PER_KB) {
            return round($this->kb(), $precision) . ' KB';
        }

        return $this->bytes . ' B';
    }

    public function isZero(): bool
    {
        return $this->bytes === 0;
    }

    public function add(self $other): self
    {
        return new self($this->bytes + $other->bytes);
    }

    public function subtract(self $other): self
    {
        $result = $this->bytes - $other->bytes;

        return new self(max(0, $result));
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->bytes > $other->bytes;
    }

    public function isLessThan(self $other): bool
    {
        return $this->bytes < $other->bytes;
    }

    public function equals(self $other): bool
    {
        return $this->bytes === $other->bytes;
    }

    public function toArray(): array
    {
        return [
            'bytes'     => $this->bytes,
            'formatted' => $this->formatted(),
        ];
    }
}
