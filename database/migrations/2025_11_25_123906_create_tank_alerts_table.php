<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTankAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tank_alerts', function (Blueprint $table) {

            // PK + FK ke water_tank(unit_id)
            $table->unsignedBigInteger('tank_unit_id')->primary();

            // Alert columns
            $table->boolean('alert_low_water')->default(false);
            $table->boolean('alert_high_water')->default(false);
            $table->boolean('alert_pump_long_run')->default(false);
            $table->boolean('alert_no_flow')->default(false);
            $table->boolean('pump_overheat_status')->default(false);

            // updated_at manual
            $table->dateTime('updated_at')->nullable();

            // Foreign key
            $table->foreign('tank_unit_id')
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
        Schema::dropIfExists('tank_alerts');
    }
}
