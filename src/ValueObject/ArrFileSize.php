<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\ValueObject;

use MartinCamen\ArrCore\Contract\Arrayable;
use MartinCamen\PhpFileSize\Enums\ByteBase;
use MartinCamen\PhpFileSize\Enums\ConfigurationOption;
use MartinCamen\PhpFileSize\FileSize;

/**
 * FileSize extension that implements Arrayable for serialization.
 *
 * All FileSize methods are available via inheritance.
 */
final class ArrFileSize extends FileSize implements Arrayable
{
    protected static function initiate(array $options = []): static
    {
        // Set default label style as `Decimal` (`megabytes` instead of `mebibytes`)
        $options[ConfigurationOption::LabelStyle->value] ??= ByteBase::Decimal;

        return new self(options: $options);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'bytes'     => (int) $this->getBytes(),
            'formatted' => $this->format(),
        ];
    }
}
