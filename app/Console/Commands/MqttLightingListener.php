<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Lightning\LightingSetting;
use App\Models\Lightning\LightingUnit;
use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttLightingListener extends Command
{
    protected $signature = 'mqtt:lighting-listen';
    protected $description = 'Listen lighting MQTT topics';

    protected $mqtt;

    public function handle()
    {
        $server   = 'broker.emqx.io';
        $port     = 1883;
        $clientId = 'laravel-listener-lighting-'.uniqid();
        $username = null;
        $password = null;

        // Init client
        $this->mqtt = new MqttClient($server, $port, $clientId);

        // Connection Options
        $settings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setKeepAliveInterval(60)
            ->setUseTls(false);

        // Connect
        $this->mqtt->connect($settings, true);
        $this->info("MQTT Connected!");

        // SUBSCRIBE sensor topics
        $this->mqtt->subscribe('lighting/+/sensor', function ($topic, $message) {
            $this->handleSensorMessage($topic, $message);
        }, 1);

        // Loop forever
        $this->mqtt->loop(true);
    }

    /**
     * Handle incoming sensor message
     */
    
    private function handleSensorMessage($topic, $message)
    {
        echo "MESSAGE RECEIVED: $topic => $message\n";

        // Example: lighting/unit01/sensor
        $deviceCode = explode('/', $topic)[1];

        // GET device
        $device = Device::where('device_code', $deviceCode)->first();
        if (!$device) {
            echo "DEVICE NOT FOUND: $deviceCode\n";
            return;
        }

        // lighting_units.unit_id = devices.id
        $unitId = $device->id;

        // Decode JSON dari ESP
        $data = json_decode($message, true);
        if (!$data) {
            echo "Invalid JSON\n";
            return;
        }

        // UPDATE DATABASE
        $unit = LightingUnit::updateOrCreate(
            ['unit_id' => $unitId],
            [
                'current_lux' => $data['lux'] ?? null,
                'lamp_status' => $data['lamp_status'] ?? 'OFF',
                'mode'        => $data['mode'] ?? 'MANUAL',
                'last_schedule_check' => now(),
                'updated_at' => now()
            ]
        );

        echo "UPDATED lighting_units for device_code=$deviceCode (unit_id=$unitId)\n";

        // Cek apakah ada setting-nya
        $settings = LightingSetting::where('unit_id', $unitId)->first();
        if (!$settings) {
            echo "NO SETTINGS for unit_id $unitId\n";
            return;
        }

        $mode = $unit->mode;

        // ============================
        // AUTO LUX MODE
        // ============================
        if ($mode === 'AUTO_LUX') {

            $lux = $data['lux'];

            if ($lux < $settings->lux_threshold) {
                $this->sendCommand($deviceCode, "ON");
                $unit->lamp_status = "ON";
                $unit->save();
                echo "AUTO_LUX → Lamp ON (lux $lux)\n";
            }

            if ($lux > $settings->lux_threshold + 10) {
                $this->sendCommand($deviceCode, "OFF");
                $unit->lamp_status = "OFF";
                $unit->save();
                echo "AUTO_LUX → Lamp OFF (lux $lux)\n";
            }
        }

        // ============================
        // AUTO TIME MODE
        // ============================
        if ($mode === 'AUTO_TIME') {

            $now = now();
            $day = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][$now->dayOfWeekIso - 1];

            $activeDays = explode(',', $settings->active_days);

            if (!in_array($day, $activeDays)) {
                echo "AUTO_TIME → Hari tidak aktif ($day)\n";
                return;
            }

            $onTime  = \Carbon\Carbon::createFromFormat('H:i', $settings->on_time);
            $offTime = \Carbon\Carbon::createFromFormat('H:i', $settings->off_time);

            if ($now->between($onTime, $offTime)) {
                $this->sendCommand($deviceCode, "ON");
                $unit->lamp_status = "ON";
                $unit->schedule_active = 1;
            } else {
                $this->sendCommand($deviceCode, "OFF");
                $unit->lamp_status = "OFF";
                $unit->schedule_active = 0;
            }

            $unit->save();
        }

        echo "------------------------------------------\n";
    }


    /**
     * SEND MQTT COMMAND
     * Must use device_code, NOT unitId
     */
    private function sendCommand($deviceCode, $cmd)
    {
        $topic = "lighting/$deviceCode/command";

        // ESP expects JSON format
        $this->mqtt->publish($topic, json_encode([
            "command" => $cmd
        ]), 1);

        echo "MQTT → SEND: $topic = $cmd\n";
    }

}
