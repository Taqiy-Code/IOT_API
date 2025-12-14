<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTankRealtimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tank_realtime', function (Blueprint $table) {

            // tank_unit_id sebagai Primary Key & Foreign Key
            $table->unsignedBigInteger('tank_unit_id')->primary();

            // Kolom data realtime
            $table->float('water_level_cm');           // Tinggi air (cm)
            $table->integer('water_level_percent');    // Persentase
            $table->float('distance_cm');              // Jarak sensor ke permukaan air
            $table->float('flow_rate_lpm');            // Debit air (liter per menit)
            $table->float('total_liters_today');       // Akumulasi pemakaian harian
            $table->float('total_liters_alltime');     // Akumulasi total
            $table->boolean('is_water_flowing');       // Air mengalir atau tidak

            // pump_status bisa boolean atau string (default: OFF)
            $table->string('pump_status', 10)->default('OFF');

            // timestamps optional
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Relasi FK ke water_tank(unit_id)
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
        Schema::dropIfExists('tank_realtime');
    }
}
