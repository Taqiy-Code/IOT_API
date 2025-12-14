<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemperatureRealtimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temperature_realtime', function (Blueprint $table) {

            // Primary Key + Foreign Key
            $table->unsignedBigInteger('unit_id')->primary();

            // Data Realtime
            $table->float('room_temperature_c');        // Suhu ruangan
            $table->float('room_humidity_percent');     // Kelembapan ruangan
            $table->string('comfort_status');           // Status kenyamanan
            $table->integer('signal_strength');         // Sinyal WiFi (dBm)

            // Waktu update
            $table->timestamp('updated_at')->nullable();

            // Foreign Key ke temperature_unit(temperature_unit_id)
            $table->foreign('unit_id')
                ->references('temperature_unit_id')
                ->on('temperature_unit')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temperature_realtime');
    }
}
