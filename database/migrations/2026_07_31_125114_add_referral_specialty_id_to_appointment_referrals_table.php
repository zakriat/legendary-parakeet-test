<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'appointment_referrals',
            function (Blueprint $table) {
                $table->foreignId('referral_specialty_id')->nullable()->after('receiving_doctor_id')
                        ->constrained('referral_specialties')->nullOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'appointment_referrals',
            function (Blueprint $table) {
                $table->dropForeign([
                    'referral_specialty_id',
                ]);

                $table->dropColumn(
                    'referral_specialty_id'
                );
            }
        );
    }
};