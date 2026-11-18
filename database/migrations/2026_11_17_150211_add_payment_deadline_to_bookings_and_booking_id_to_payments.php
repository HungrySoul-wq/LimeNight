<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Добавляем срок ожидания оплаты для бронирования.
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('expires_at')
                ->nullable()
                ->after('status');

            $table->index(['status', 'expires_at']);
        });

        // Связываем платеж с конкретным бронированием.
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('booking_id')
                ->nullable()
                ->after('user_id')
                ->constrained('bookings')
                ->nullOnDelete();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['booking_id', 'status']);
            $table->dropConstrainedForeignId('booking_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['status', 'expires_at']);
            $table->dropColumn('expires_at');
        });
    }
};
