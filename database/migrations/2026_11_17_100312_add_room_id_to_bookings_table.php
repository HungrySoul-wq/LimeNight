<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Разрешаем бронирование без конкретного компьютера.
            // Это нужно для бронирования всей комнаты.
            $table->foreignId('computer_id')
                ->nullable()
                ->change();

            // Комната, которую бронирует клиент.
            $table->foreignId('room_id')
                ->nullable()
                ->after('computer_id')
                ->constrained('rooms')
                ->nullOnDelete();

            // Тип бронирования: computer или room.
            $table->string('booking_type', 30)
                ->default('computer')
                ->after('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('room_id');
            $table->dropColumn('booking_type');

            // Возвращаем обязательный computer_id.
            $table->foreignId('computer_id')->nullable(false)->change();
        });
    }
};
