<?php

namespace MartinCamen\ArrCore\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\CommandEndpoint;
use MartinCamen\ArrCore\Data\Enums\CommandName;
use MartinCamen\ArrCore\Data\Responses\Command;

/** @link https://radarr.video/docs/api/#/Command */
readonly class CommandActions
{
    public function __construct(protected RestClientInterface $client) {}

    /** @return array<string, mixed> */
    public function all(): array
    {
        $result = $this->client->get(CommandEndpoint::All);

        return array_map(
            Command::fromArray(...),
            $result ?? [],
        );
    }

    /**
     * Get command by ID.
     *
     * @link https://radarr.video/docs/api/#/Command/get_api_v3_command__id_
     */
    public function get(int $id): Command
    {
        $result = $this->client->get(CommandEndpoint::ById, ['id' => $id]);

        return Command::fromArray($result);
    }

    /**
     * Execute a command.
     *
     * @param array<string, mixed> $body
     *
     * @link https://radarr.video/docs/api/#/Command/post_api_v3_command
     */
    public function run(CommandName $name, array $body = []): Command
    {
        $result = $this->client->post(CommandEndpoint::All, array_merge(
            ['name' => $name->value],
            $body,
        ));

        return Command::fromArray($result);
    }

    /**
     * Cancel a running command.
     *
     * @link https://radarr.video/docs/api/#/Command/delete_api_v3_command__id_
     */
    public function cancel(int $id): void
    {
        $this->client->delete(CommandEndpoint::ById, ['id' => $id]);
    }

    /** Trigger RSS sync. */
    public function rssSync(): Command
    {
        return $this->run(CommandName::RssSync);
    }

    /** Create a backup. */
    public function backup(): Command
    {
        return $this->run(CommandName::Backup);
    }

    /**
     * Rename specific files.
     *
     * @param array<int, int> $files
     */
    public function renameFiles(int $id, array $files): Command
    {
        return $this->run(CommandName::RenameFiles, [
            'movieId' => $id,
            'files'   => $files,
        ]);
    }
}
