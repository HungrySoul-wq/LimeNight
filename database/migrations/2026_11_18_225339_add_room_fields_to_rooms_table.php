<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'zone_id')) {
                $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('rooms', 'name')) {
                $table->string('name')->nullable();
            }

            if (!Schema::hasColumn('rooms', 'type')) {
                $table->string('type')->nullable();
            }

            if (!Schema::hasColumn('rooms', 'capacity')) {
                $table->unsignedInteger('capacity')->default(1);
            }

            if (!Schema::hasColumn('rooms', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Существующие данные и столбцы не удаляем.
        });
    }
};
