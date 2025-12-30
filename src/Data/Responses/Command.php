<?php

declare(strict_types=1);

namespace MartinCamen\ArrCore\Data\Responses;

/**
 * Represents a command response from *arr APIs.
 *
 * This is a shared data structure used by both Radarr and Sonarr.
 */
final readonly class Command
{
    /** @param array<string, mixed>|null $body */
    public function __construct(
        public int $id,
        public string $name,
        public string $commandName,
        public string $status,
        public string $priority,
        public ?string $queued,
        public ?string $started,
        public ?string $ended,
        public ?string $stateChangeTime,
        public ?string $trigger,
        public bool $sendUpdatesToClient,
        public bool $updateScheduledTask,
        public ?array $body,
        public ?string $message,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            commandName: $data['commandName'] ?? '',
            status: $data['status'] ?? 'unknown',
            priority: $data['priority'] ?? 'normal',
            queued: $data['queued'] ?? null,
            started: $data['started'] ?? null,
            ended: $data['ended'] ?? null,
            stateChangeTime: $data['stateChangeTime'] ?? null,
            trigger: $data['trigger'] ?? null,
            sendUpdatesToClient: $data['sendUpdatesToClient'] ?? false,
            updateScheduledTask: $data['updateScheduledTask'] ?? false,
            body: $data['body'] ?? null,
            message: $data['message'] ?? null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->name,
            'command_name'           => $this->commandName,
            'status'                 => $this->status,
            'priority'               => $this->priority,
            'queued'                 => $this->queued,
            'started'                => $this->started,
            'ended'                  => $this->ended,
            'state_change_time'      => $this->stateChangeTime,
            'trigger'                => $this->trigger,
            'send_updates_to_client' => $this->sendUpdatesToClient,
            'update_scheduled_task'  => $this->updateScheduledTask,
            'body'                   => $this->body,
            'message'                => $this->message,
        ];
    }

    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    public function isStarted(): bool
    {
        return $this->status === 'started';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRunning(): bool
    {
        return $this->isQueued() || $this->isStarted();
    }
}
