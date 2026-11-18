<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // room_id, его внешний ключ и индекс zone_id
        // уже созданы предыдущими попытками миграции.
        // Поэтому повторно их не добавляем.

        Schema::table('computers', function (Blueprint $table) {

            // Удаляем старое ограничение:
            // номер компьютера был уникальным во всей зоне.
            $table->dropUnique('computers_zone_id_pc_number_unique');

            // Создаём новое ограничение:
            // номер компьютера уникален только внутри комнаты.
            $table->unique(
                ['room_id', 'pc_number'],
                'computers_room_id_pc_number_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('computers', function (Blueprint $table) {

            // Удаляем новый уникальный индекс.
            $table->dropUnique('computers_room_id_pc_number_unique');

            // Возвращаем старый уникальный индекс.
            $table->unique(
                ['zone_id', 'pc_number'],
                'computers_zone_id_pc_number_unique'
            );
        });
    }
};
