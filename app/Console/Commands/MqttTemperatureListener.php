<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\DB;
use App\Events\TemperatureUpdated;

class MqttTemperatureListener extends Command
{
    protected $signature = 'mqtt:temperature-listen';
    protected $description = 'Listen to MQTT topic and save temperature data to database';

    public function handle()
    {
        $this->info("🚀 Starting MQTT listener...");

        $server   = 'broker.emqx.io';
        $port     = 1883;
        $clientId = 'laravel_listener_' . uniqid();
        $topic    = 'sensor/dht22';

        $mqtt = new MqttClient($server, $port, $clientId);

        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(60)
            ->setLastWillTopic($topic)
            ->setLastWillMessage('{"status":"offline"}')
            ->setLastWillQualityOfService(0);

        $mqtt->connect($connectionSettings, true);
        $this->info("✅ Connected to MQTT broker at $server:$port");

        $mqtt->subscribe($topic, function ($topic, $message) {

            $this->info("📥 Received message: $message");

            $data = json_decode($message, true);

            if (!$data || !isset($data['device_code'])) {
                $this->error("❌ Invalid payload or missing device_code");
                return;
            }

            $device = DB::table('devices')
                ->where('device_code', $data['device_code'])
                ->first();

            if (!$device) {
                $this->error("❌ Device not found: " . $data['device_code']);
                return;
            }

            $now = now();

            DB::table('temperature_realtime')->updateOrInsert(
                ['unit_id' => $device->id],
                [
                    'room_temperature_c'    => $data['temperature'] ?? 0,
                    'room_humidity_percent' => $data['humidity'] ?? 0,
                    'comfort_status'        => $data['comfort_status'] ?? 'normal',
                    'signal_strength'       => $data['signal_strength'] ?? 0,
                    'created_at'            => DB::raw('IFNULL(created_at, NOW())'),
                    'updated_at'            => $now,
                ]
            );

            event(new TemperatureUpdated([
                'unit_id' => $device->id,
                'device_name' => $device->name,
                'temperature' => $data['temperature'],
                'humidity' => $data['humidity'],
                'comfort_status' => $data['comfort_status'] ?? 'normal',
            ]));

            $this->info("✅ Data inserted/updated & broadcasted for unit_id: " . $device->id);
        }, 0);

        while (true) {
            $mqtt->loop();
            usleep(100000);
        }
    }
}