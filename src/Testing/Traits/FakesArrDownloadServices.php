<?php

namespace MartinCamen\ArrCore\Testing\Traits;

use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemSummary;
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

    protected function getStatusForDownloadServiceSystemSummary(): DownloadServiceSystemSummary
    {
        if (isset($this->responses['systemSummary'])) {
            return DownloadServiceSystemSummary::fromArray($this->responses['systemSummary']);
        }

        return DownloadServiceSystemSummary::fromArray(ArrSystemStatusFactory::make());
    }

    protected function getHealthForDownloadServiceSystemSummary(): HealthCheckCollection
    {
        if (isset($this->responses['systemSummary'])) {
            return HealthCheckCollection::fromArray($this->responses['health'] ?? []);
        }

        return HealthCheckCollection::fromArray([]);
    }
}
