<?php

namespace MartinCamen\ArrCore\Testing\Traits;

use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemStatus;
use MartinCamen\ArrCore\Domain\System\HealthCheckCollection;
use MartinCamen\ArrCore\Testing\Factories\ArrDownloadFactory;
use MartinCamen\ArrCore\Testing\Factories\ArrSystemStatusFactory;

trait FakesArrDownloadServices
{
    protected function formatsDownloads(): array
    {
        return $this->responses['downloads'] ?? [
            'page'         => 1,
            'pageSize'     => 10,
            'totalRecords' => 2,
            'records'      => ArrDownloadFactory::makeMany(2),
        ];
    }

    protected function getStatusForDownloadServiceSystemStatus(): DownloadServiceSystemStatus
    {
        if (isset($this->responses['systemStatus'])) {
            return DownloadServiceSystemStatus::fromArray($this->responses['systemStatus']);
        }

        return DownloadServiceSystemStatus::fromArray(ArrSystemStatusFactory::make());
    }

    protected function getHealthForDownloadServiceSystemStatus(): HealthCheckCollection
    {
        if (isset($this->responses['systemStatus'])) {
            return HealthCheckCollection::fromArray($this->responses['health'] ?? []);
        }

        return HealthCheckCollection::fromArray([]);
    }
}
