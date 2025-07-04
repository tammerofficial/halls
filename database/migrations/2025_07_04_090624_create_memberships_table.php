<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->boolean('is_active')->default(true);
            $table->json('benefits')->nullable();
            $table->integer('max_halls')->default(1);
            $table->integer('max_bookings_per_month')->default(10);
            $table->boolean('priority_support')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('advanced_analytics')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
