<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLightingSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lighting_settings', function (Blueprint $table) {
            // PK + FK ke lighting_units.unit_id
            $table->unsignedBigInteger('unit_id')->primary();
            // Threshold lux
            $table->float('lux_threshold')->default(30);
            // Delay auto ON / OFF
            $table->integer('auto_on_delay_sec')->default(0);
            $table->integer('auto_off_delay_sec')->default(0);
            // Schedule (AUTO_TIME)
            $table->time('on_time')->nullable();
            $table->time('off_time')->nullable();
            // Hari aktif (Mon,Tue,Wed,...)
            $table->string('active_days')->nullable();
            // Izinkan manual override
            $table->boolean('allow_manual_override')->default(true);
            $table->timestamps();
            // Foreign Key
            $table->foreign('unit_id')
                ->references('unit_id')
                ->on('lighting_units')
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
        Schema::dropIfExists('lighting_settings');
    }
}
