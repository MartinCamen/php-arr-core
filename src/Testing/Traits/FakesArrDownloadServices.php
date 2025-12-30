<?php

namespace MartinCamen\ArrCore\Testing;

use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemStatus;
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use MartinCamen\Sonarr\Data\Responses\HealthCheckCollection;
use MartinCamen\Sonarr\Testing\Factories\SystemStatusFactory;

trait FakesArrDownloadServices
{
    protected function formatsDownloads(): array
    {
        return $this->responses['downloads'] ?? [
            'page'         => 1,
            'pageSize'     => 10,
            'totalRecords' => 2,
            'records'      => DownloadFactory::makeMany(2),
        ];
    }

    protected function getStatusForDownloadServiceSystemStatus(): DownloadServiceSystemStatus
    {
        if (isset($this->responses['systemStatus'])) {
            return DownloadServiceSystemStatus::fromArray($this->responses['systemStatus']);
        }

        return DownloadServiceSystemStatus::fromArray(SystemStatusFactory::make());
    }

    protected function getHealthForDownloadServiceSystemStatus(): HealthCheckCollection
    {
        if (isset($this->responses['systemStatus'])) {
            return HealthCheckCollection::fromArray($this->responses['health'] ?? []);
        }

        return HealthCheckCollection::fromArray([]);
    }
}
