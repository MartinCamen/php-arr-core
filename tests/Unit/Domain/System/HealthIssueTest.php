<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Tests\Unit\Domain\System;

use MartinCamen\ArrCore\Domain\System\HealthIssue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HealthIssueTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $issue = new HealthIssue(
            type: 'warning',
            message: 'Root folder is missing',
            source: 'RootFolderCheck',
            wikiUrl: 'https://wiki.servarr.com/sonarr/system/health-checks',
        );

        $this->assertSame('warning', $issue->type);
        $this->assertSame('Root folder is missing', $issue->message);
        $this->assertSame('RootFolderCheck', $issue->source);
        $this->assertSame('https://wiki.servarr.com/sonarr/system/health-checks', $issue->wikiUrl);
    }

    #[Test]
    public function canBeCreatedWithMinimalData(): void
    {
        $issue = new HealthIssue(
            type: 'error',
            message: 'Something went wrong',
        );

        $this->assertSame('error', $issue->type);
        $this->assertSame('Something went wrong', $issue->message);
        $this->assertSame('', $issue->source);
        $this->assertSame('', $issue->wikiUrl);
    }

    #[Test]
    public function canBeCreatedFromArray(): void
    {
        $issue = HealthIssue::fromArray([
            'type'     => 'warning',
            'message'  => 'Indexer unavailable',
            'source'   => 'IndexerStatusCheck',
            'wiki_url' => 'https://wiki.servarr.com/radarr',
        ]);

        $this->assertSame('warning', $issue->type);
        $this->assertSame('Indexer unavailable', $issue->message);
        $this->assertSame('IndexerStatusCheck', $issue->source);
        $this->assertSame('https://wiki.servarr.com/radarr', $issue->wikiUrl);
    }

    #[Test]
    public function handlesPartialArrayData(): void
    {
        $issue = HealthIssue::fromArray([
            'type'    => 'error',
            'message' => 'Critical error',
        ]);

        $this->assertSame('error', $issue->type);
        $this->assertSame('Critical error', $issue->message);
        $this->assertSame('', $issue->source);
        $this->assertSame('', $issue->wikiUrl);
    }

    #[Test]
    public function handlesMissingTypeInArray(): void
    {
        $issue = HealthIssue::fromArray([
            'message' => 'Unknown issue',
        ]);

        $this->assertSame('unknown', $issue->type);
        $this->assertSame('Unknown issue', $issue->message);
    }

    #[Test]
    public function canBeConvertedToArray(): void
    {
        $issue = new HealthIssue(
            type: 'warning',
            message: 'Download client unavailable',
            source: 'DownloadClientCheck',
            wikiUrl: 'https://wiki.servarr.com/sonarr',
        );

        $array = $issue->toArray();

        $this->assertSame('warning', $array['type']);
        $this->assertSame('Download client unavailable', $array['message']);
        $this->assertSame('DownloadClientCheck', $array['source']);
        $this->assertSame('https://wiki.servarr.com/sonarr', $array['wiki_url']);
    }
}
