<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterTankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_tank', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id')->primary();

            $table->integer('min_level_percent');
            $table->integer('max_level_percent');
            $table->boolean('auto_mode')->default(false);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

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
        Schema::dropIfExists('water_tank');
    }
}
