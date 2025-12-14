<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLightingUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lighting_units', function (Blueprint $table) {

            // PK & FK ke devices.id
            $table->unsignedBigInteger('unit_id')->primary();

            // Sensor BH1750
            $table->float('current_lux')->nullable();

            // Status lampu: ON / OFF
            $table->enum('lamp_status', ['ON', 'OFF'])->default('OFF');

            // Mode lampu: AUTO_LUX, AUTO_TIME, MANUAL
            $table->enum('mode', ['AUTO_LUX', 'AUTO_TIME', 'MANUAL'])->default('MANUAL');

            // Jadwal aktif atau tidak
            $table->boolean('schedule_active')->default(false);

            // Waktu pengecekan jadwal terakhir
            $table->timestamp('last_schedule_check')->nullable();

            // created_at & updated_at
            $table->timestamps();

            // Foreign key → devices.id
            $table->foreign('unit_id')
                ->references('id')
                ->on('devices')
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
        Schema::dropIfExists('lighting_units');
    }
}
