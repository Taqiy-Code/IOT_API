<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_code')->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('device_type'); // lamp, water_level, temperature, dll
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
