<?php

declare(strict_types=1);

namespace App\Client\Http;

use App\Adapter\Config\ConfigInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class NotificationClient
{
    public function __construct(
        private ConfigInterface $config,
        private ClientInterface $client
    ) {
    }

    public function request(): ?array
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->config->get('client.http.notification.host') . $this->config->get('client.http.notification.path'),
                [
                    'timeout' => $this->config->get(
                        'client.http.notification.timeout',
                    ),
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $clientException) {
            return null;
        } catch (ServerException $serverException) {
            return null;
        }
    }
}
