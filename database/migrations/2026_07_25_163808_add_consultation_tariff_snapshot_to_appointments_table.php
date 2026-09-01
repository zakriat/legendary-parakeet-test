<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('consultation_tariff_id')
                ->nullable()
                ->after('service_id');

            $table->string('consultation_mode', 30)
                ->nullable()
                ->after('consultation_tariff_id');

            $table->string('rate_type', 30)
                ->nullable()
                ->after('consultation_mode');

            $table->string('tariff_name')
                ->nullable()
                ->after('rate_type');

            $table->decimal('tariff_price', 10, 2)
                ->nullable()
                ->after('tariff_name');

            $table->string('deposit_type', 20)
                ->nullable()
                ->after('tariff_price');

            $table->decimal('deposit_value', 10, 2)
                ->nullable()
                ->after('deposit_type');

            $table->decimal('deposit_amount', 10, 2)
                ->nullable()
                ->default(0)
                ->after('deposit_value');

            $table->index(
                'consultation_tariff_id',
                'appointments_tariff_id_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_tariff_id_index');

            $table->dropColumn([
                'consultation_tariff_id',
                'consultation_mode',
                'rate_type',
                'tariff_name',
                'tariff_price',
                'deposit_type',
                'deposit_value',
                'deposit_amount',
            ]);
        });
    }
};