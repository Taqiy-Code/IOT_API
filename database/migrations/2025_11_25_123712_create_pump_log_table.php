<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePumpLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump_log', function (Blueprint $table) {

            // Primary Key
            $table->id('log_id');

            // Foreign Key ke water_tank(unit_id)
            $table->unsignedBigInteger('tank_id');

            // Action ON / OFF
            $table->string('action', 5); // cukup 5 karakter

            // Timestamp kejadian
            $table->dateTime('timestamp');

            // FK
            $table->foreign('tank_id')
                ->references('unit_id')
                ->on('water_tank')
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
        Schema::dropIfExists('pump_log');
    }
}
