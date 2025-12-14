<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemperatureAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temperature_alerts', function (Blueprint $table) {

            // Primary Key + Foreign Key
            $table->unsignedBigInteger('temperature_unit_id')->primary();

            // Alert flags
            $table->boolean('alert_high_temp')->default(false);
            $table->boolean('alert_high_humidity')->default(false);
            $table->boolean('alert_low_humidity')->default(false);

            // Waktu update alert
            $table->timestamp('updated_at')->nullable();

            // Foreign Key ke temperature_unit
            $table->foreign('temperature_unit_id')
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
        Schema::dropIfExists('temperature_alerts');
    }
}
