<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Добавляем характеристики комнат
        Schema::table('rooms', function (Blueprint $table) {
            // К какой зоне относится комната
            $table->foreignId('zone_id')
                ->after('id')
                ->constrained('zones')
                ->cascadeOnDelete();

            // Название комнаты: например, Общий зал 1
            $table->string('name', 100)->after('zone_id');

            // Тип комнаты: standard, vip или bootcamp
            $table->string('type', 30)->default('standard')->after('name');

            // Вместимость комнаты
            $table->unsignedSmallInteger('capacity')->default(16)->after('type');

            // Активна ли комната
            $table->boolean('is_active')->default(true)->after('capacity');

            // Номер комнаты не должен повторяться внутри одной зоны
            $table->unique(['zone_id', 'name']);
        });
    }

    public function down(): void
    {
        // Удаляем добавленные поля при откате миграции
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropUnique(['zone_id', 'name']);
            $table->dropConstrainedForeignId('zone_id');
            $table->dropColumn([
                'name',
                'type',
                'capacity',
                'is_active',
            ]);
        });
    }
};
