<?php

namespace MartinCamen\ArrCore\Config;

/** @phpstan-consistent-constructor */
abstract class ArrServiceConfiguration
{
    public int $port = 0;
    public string $apiVersion = '';

    public function __construct(
        public string $host,
        int $port = 0,
        public string $apiKey = '',
        public bool $useHttps = false,
        public int $timeout = 30,
        public string $urlBase = '',
        ?string $apiVersion = null,
    ) {
        $this->port = $port ?: $this->port;
        $this->apiVersion = $apiVersion ?: $this->apiVersion;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        return new static(
            host: $data['host'] ?? 'localhost',
            port: $data['port'] ?? 0,
            apiKey: $data['api_key'] ?? '',
            useHttps: $data['use_https'] ?? false,
            timeout: $data['timeout'] ?? 30,
            urlBase: $data['url_base'] ?? '',
            apiVersion: $data['api_version'] ?? null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'host'        => $this->host,
            'port'        => $this->port,
            'api_key'     => $this->apiKey,
            'use_https'   => $this->useHttps,
            'timeout'     => $this->timeout,
            'url_base'    => $this->urlBase,
            'api_version' => $this->apiVersion,
        ];
    }

    public function getBaseUrl(): string
    {
        $scheme = $this->useHttps ? 'https' : 'http';
        $urlBase = trim($this->urlBase, '/');
        $urlBase = $urlBase !== '' ? "/{$urlBase}" : '';

        return "{$scheme}://{$this->host}:{$this->port}{$urlBase}/api/{$this->apiVersion}";
    }
}
