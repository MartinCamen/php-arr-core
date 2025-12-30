<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Load a JSON fixture file.
     *
     * @return array<string, mixed>
     */
    protected function loadFixture(string $filename): array
    {
        $path = __DIR__ . '/Fixtures/' . $filename;

        if (! file_exists($path)) {
            $this->fail("Fixture file not found: {$filename}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            $this->fail("Could not read fixture file: {$filename}");
        }

        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->fail("Invalid JSON in fixture file: {$filename}");
        }

        return $data;
    }
}
