<?php

namespace Bboxlab\Moselle\Client;

use Bboxlab\Moselle\Exception\BouyguesHttpBadRequestException;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MoselleClient implements HttpClientInterface
{
    use DecoratorTrait;

    private HttpClientInterface $decoratedClient;

    public function requestBtOpenApi(string $method, string $url, array $options = []): array
    {
        $response = $this->request($method, $url, $options);

        if ($code = $response->getStatusCode() >= 300) {
            $this->handleBtRequestError($code, $response->toArray(false));
        }

        return $response->toArray();
    }

    public function getContent(HttpClientInterface $client, string $method, string $url, array $options = []): string
    {
        $response = $client->request($method, $url, $options);

        $code = $response->getStatusCode();
        if ($code >= 300) {
            $this->handleBtRequestError($code, $response->toArray(false));
        }

        return $response->getContent();
    }

    /**
     * handle bt error, the bt error format is "error", "error_description", "error_parameters"
     *
     * @throws BouyguesHttpBadRequestException
     */
    private function handleBtRequestError(int $code, array $error = [])
    {
        $name = 'Unknown error';
        $description = 'No description has been returned by bt API.';
        $parameters = [];

        if (isset($error['error'])) {
            $name = $error['error'];
        };

        if (isset($error['error_description'])) {
            $description = $error['error_description'];
        };

        if (isset($error['error_parameters'])) {
            $parameters = $error['error_parameters'];
        }

        throw new BouyguesHttpBadRequestException(
            $name,
            null,
            $code,
            [],
            $description,
            $parameters,
            BouyguesHttpBadRequestException::BT_SOURCE
        );
    }

    /**
     * For using bt api in test we need to add some headers
     * from Moselle
     *
     * @throws BouyguesHttpBadRequestException
     */
    protected function addBouyguesTestHeaders(array $options, string $url):array
    {
        if (str_contains($url, '.sandbox.'))
        {
            if (!isset($options['headers']))
                $options['headers'] = [];
            $options['headers']['x-version'] = 4;

            // depending the bt test env, headers values need to be changed
            if (str_contains($url,'ap4')) {
                $options['headers']['x-banc'] = 'ap23';
            } else if (str_contains($url,'ap3')){
                $options['headers']['x-banc'] = 'ap21';
            } else {
                throw new BouyguesHttpBadRequestException(
                    'No corresponding "AP" env has been found in bt url oauth test url. "AP" known env are: AP3 and AP4'
                );
            }
        }

        return $options;
    }
}
