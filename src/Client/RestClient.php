<?php

namespace MartinCamen\ArrCore\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use MartinCamen\ArrCore\Contract\ArrServiceConfigurationContract;
use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Exceptions\AuthenticationException;
use MartinCamen\ArrCore\Exceptions\ConnectionException;
use MartinCamen\ArrCore\Exceptions\NotFoundException;
use MartinCamen\ArrCore\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

class RestClient implements RestClientInterface
{
    protected Client $httpClient;

    public function __construct(
        protected ArrServiceConfigurationContract $config,
        ?Client $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? new Client([
            'timeout' => $this->config->timeout,
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'X-Api-Key'    => $this->config->apiKey,
            ],
        ]);
    }

    /** @param array<string, mixed> $params */
    public function get(Endpoint $endpoint, array $params = []): mixed
    {
        return $this->request('GET', $endpoint, $params);
    }

    /** @param array<string, mixed> $data */
    public function post(Endpoint $endpoint, array $data = []): mixed
    {
        return $this->request('POST', $endpoint, body: $data);
    }

    /** @param array<string, mixed> $data */
    public function put(Endpoint $endpoint, array $data = []): mixed
    {
        return $this->request('PUT', $endpoint, body: $data);
    }

    /** @param array<string, mixed> $params */
    public function delete(Endpoint $endpoint, array $params = []): mixed
    {
        return $this->request('DELETE', $endpoint, $params);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     *
     * @throws AuthenticationException
     * @throws ConnectionException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws GuzzleException
     */
    protected function request(
        string $method,
        Endpoint $endpoint,
        array $query = [],
        array $body = [],
    ): mixed {
        $pathParams = $this->extractPathParams($endpoint, $query);
        $url = $this->buildUrl($endpoint, $pathParams);

        $options = [
            'headers' => [
                'X-Api-Key' => $this->config->apiKey,
            ],
        ];

        if ($query !== [] && in_array($method, ['GET', 'DELETE'])) {
            $options['query'] = $query;
        }

        if ($body !== [] && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options['json'] = $body;
        }

        try {
            $response = $this->httpClient->request($method, $url, $options);

            $contents = $response->getBody()->getContents();

            if ($contents === '') {
                return null;
            }

            return json_decode($contents, true);
        } catch (ConnectException $e) {
            throw ConnectionException::failed(
                $this->config->host,
                $this->config->port,
                $e->getMessage(),
            );
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if (! $response instanceof ResponseInterface) {
                throw ConnectionException::failed(
                    $this->config->host,
                    $this->config->port,
                    $e->getMessage(),
                );
            }

            $status = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            return match ($status) {
                401 => throw AuthenticationException::invalidApiKey(),
                403 => throw AuthenticationException::unauthorized(),
                404 => throw NotFoundException::resourceNotFound($endpoint->path($pathParams)),
                400, 422 => throw ValidationException::fromResponse($responseBody),
                default => throw ConnectionException::failed(
                    $this->config->host,
                    $this->config->port,
                    $e->getMessage(),
                ),
            };
        }
    }

    /**
     * Extract path parameters from query and return them separately.
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    protected function extractPathParams(Endpoint $endpoint, array &$query): array
    {
        $pathParams = [];
        $path = $endpoint->path();

        preg_match_all('/\{(\w+)\}/', $path, $matches);

        foreach ($matches[1] as $param) {
            if (isset($query[$param])) {
                $pathParams[$param] = $query[$param];
                unset($query[$param]);
            }
        }

        return $pathParams;
    }

    /** @param array<string, mixed> $pathParams */
    protected function buildUrl(Endpoint $endpoint, array $pathParams): string
    {
        $path = $endpoint->path($pathParams);

        return "{$this->config->getBaseUrl()}/{$path}";
    }
}
