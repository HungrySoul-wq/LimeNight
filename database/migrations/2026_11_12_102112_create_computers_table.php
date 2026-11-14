<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->unsignedSmallInteger('pc_number');
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 30)->default('available');
            $table->json('specs')->nullable();
            $table->timestamps();

            $table->unique(['zone_id', 'pc_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
};