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
        Schema::create('membership_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained()->onDelete('cascade');
            $table->foreignId('feature_id')->constrained()->onDelete('cascade');
            $table->json('settings')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            
            $table->unique(['membership_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_feature');
    }
};
