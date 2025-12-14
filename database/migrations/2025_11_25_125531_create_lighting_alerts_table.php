<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLightingAlertsTable extends Migration
{
    public function up()
    {
        Schema::create('lighting_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('alert_type');
            $table->text('message')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->foreign('unit_id')
                ->references('unit_id')
                ->on('lighting_units')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lighting_alerts');
    }
}
