<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLightingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lighting_logs', function (Blueprint $table) {

            // Primary key
            $table->id();

            // FK → lighting_units
            $table->unsignedBigInteger('lighting_unit_id');

            // Jenis event
            $table->enum('event_type', [
                'auto_on',
                'auto_off',
                'manual_on',
                'manual_off',
                'sensor_update'
            ]);

            // Nilai sebelum dan sesudah
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();

            // Timestamp log
            $table->timestamps();

            // FK reference
            $table->foreign('lighting_unit_id')
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
        Schema::dropIfExists('lighting_logs');
    }
}
