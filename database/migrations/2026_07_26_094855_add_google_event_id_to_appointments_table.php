<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('google_event_id')
                ->nullable()
                ->after('meet_link');

            $table->index(
                'google_event_id',
                'appointments_google_event_id_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(
                'appointments_google_event_id_index'
            );

            $table->dropColumn('google_event_id');
        });
    }
};