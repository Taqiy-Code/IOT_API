<?php

namespace App\Services;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;

class MqttService
{
    protected $mqtt;

    public function __construct()
    {
        $server = 'broker.emqx.io';
        $port = 1883;

        $settings = (new ConnectionSettings)
            ->setUsername(null)
            ->setPassword(null);

        $this->mqtt = new MqttClient($server, $port, 'laravel_server');
        $this->mqtt->connect($settings, true);
    }
    
    public function publish($topic, $message)
    {
        try {
            $clientId = 'laravel_' . rand(1000, 9999);

            $mqtt = new MqttClient(
                env('MQTT_HOST'),
                env('MQTT_PORT'),
                $clientId
            );

            $mqtt->connect();

            $mqtt->publish($topic, $message, 0);

            $mqtt->disconnect();
        } catch (MqttClientException $e) {
            return $e->getMessage();
        }
    }
}
