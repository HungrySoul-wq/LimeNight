<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->decimal('price_1_hour', 10, 2)->default(500);
            $table->decimal('price_3_hours', 10, 2)->default(1300);
            $table->decimal('price_5_hours', 10, 2)->default(2000);
            $table->decimal('price_night', 10, 2)->default(3500);
        });
    }

    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn([
                'price_1_hour',
                'price_3_hours',
                'price_5_hours',
                'price_night',
            ]);
        });
    }
};