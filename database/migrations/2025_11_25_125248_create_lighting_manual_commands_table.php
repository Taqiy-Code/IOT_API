<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLightingManualCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lighting_manual_commands', function (Blueprint $table) {

            // Primary key
            $table->id(); // id command

            // FK → lighting_units
            // $table->unsignedBigInteger('lighting_unit_id');
            $table->unsignedBigInteger('unit_id');

            // Perintah ON/OFF
            $table->enum('command', ['on', 'off']);

            // Status sudah dijalankan atau belum
            $table->boolean('executed')->default(false);

            // Waktu eksekusi
            $table->dateTime('executed_at')->nullable();

            // Waktu request
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
        Schema::dropIfExists('lighting_manual_commands');
    }
}
