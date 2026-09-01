<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'referral_specialties',
            function (Blueprint $table) {
                $table->id();

                $table->string('category');
                $table->string('name');

                $table->boolean('status')
                    ->default(true);

                $table->unsignedInteger('sort_order')
                    ->default(0);

                $table->timestamps();

                $table->unique([
                    'category',
                    'name',
                ]);

                $table->index([
                    'status',
                    'category',
                    'sort_order',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'referral_specialties'
        );
    }
};