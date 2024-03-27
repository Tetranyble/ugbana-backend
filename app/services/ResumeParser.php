<?php

namespace App\services;

use App\Interface\ResumeParserInterface;
use GuzzleHttp\Client;

class ResumeParser extends Client implements ResumeParserInterface
{
    protected string $key;

    protected string $endpoint;

    protected $options = [
        'url' => '',
    ];

    public function __construct(array $config = [])
    {

        $this->key = config('services.apilayer.key');
        $this->endpoint = config('services.apilayer.url');

        parent::__construct(array_merge([
            'base_uri' => $this->endpoint,

        ], $config));

    }

    public function parse(string $url)
    {

        return $this->sends(
            'GET',
            'url',
            ['url' => $url]
        );
    }

    /**
     * Send request to the endpoint
     *
     * @throws GuzzleException
     */
    public function sends(string $method, string $uri, $options = [])
    {

        $response = $this->request(
            $method,
            $uri,
            $this->mFilter(
                [
                    'query' => $options,
                    'headers' => [
                        'apikey' => $this->key,
                        'Content-Type' => 'text/plain',
                        'Accept' => 'application/json',
                    ],
                ]
            )
        )->getBody();

        return $this->prepareResponse($response);
    }

    public function __destruct()
    {

    }

    protected function mFilter(array $data): array
    {
        return array_filter(
            array_merge(
                $data
            )
        );
    }

    /**
     * Get json response of the request
     */
    private function prepareResponse($response): mixed
    {
        return json_decode($response->getContents());
    }
}
