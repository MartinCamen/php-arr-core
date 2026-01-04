<?php

namespace MartinCamen\ArrCore\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\SystemEndpoint;
use MartinCamen\ArrCore\Data\Responses\DiskSpaceCollection;
use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemSummary;
use MartinCamen\ArrCore\Domain\System\HealthCheckCollection;

/**
 * @link https://wiki.servarr.com/sonarr/api
 * @link https://radarr.video/docs/api/#/System
 */
readonly class SystemActions
{
    public function __construct(protected RestClientInterface $client) {}

    /**
     * Get system status.
     *
     * @link https://radarr.video/docs/api/#/System/get_api_v3_system_status
     */
    public function status(): DownloadServiceSystemSummary
    {
        $result = $this->client->get(SystemEndpoint::Status);

        return DownloadServiceSystemSummary::fromArray($result);
    }

    /**
     * Get health check results.
     *
     * @link https://radarr.video/docs/api/#/Health/get_api_v3_health
     */
    public function health(): HealthCheckCollection
    {
        $result = $this->client->get(SystemEndpoint::Health);

        return HealthCheckCollection::fromArray($result);
    }

    /**
     * Get disk space information.
     *
     * @link https://radarr.video/docs/api/#/DiskSpace/get_api_v3_diskspace
     */
    public function diskSpace(): DiskSpaceCollection
    {
        $result = $this->client->get(SystemEndpoint::DiskSpace);

        return DiskSpaceCollection::fromArray($result);
    }

    /**
     * Get scheduled tasks.
     *
     * @return array<int, array<string, mixed>>
     *
     * @link https://radarr.video/docs/api/#/Task/get_api_v3_system_task
     */
    public function tasks(): array
    {
        return $this->client->get(SystemEndpoint::Task);
    }

    /**
     * Get a specific scheduled task.
     *
     * @return array<string, mixed>
     *
     * @link https://radarr.video/docs/api/#/Task/get_api_v3_system_task__id_
     */
    public function task(int $id): array
    {
        return $this->client->get(SystemEndpoint::TaskById, ['id' => $id]);
    }

    /**
     * Get available backups.
     *
     * @return array<int, array<string, mixed>>
     *
     * @link https://radarr.video/docs/api/#/Backup/get_api_v3_system_backup
     */
    public function backups(): array
    {
        return $this->client->get(SystemEndpoint::Backup);
    }
}
