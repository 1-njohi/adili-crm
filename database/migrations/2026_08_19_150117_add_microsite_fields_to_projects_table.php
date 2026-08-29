<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('hero_description')->nullable();
            $table->text('hero_description_2')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->decimal('starting_price', 15, 2)->nullable();
            $table->string('plot_size')->nullable();
            $table->string('drone_video_id')->nullable();
            $table->string('brochure_path')->nullable();
            $table->json('feature_groups')->nullable();
            $table->json('amenities')->nullable();
            $table->decimal('min_deposit', 15, 2)->nullable();
            $table->integer('max_months')->nullable();
            $table->json('payment_tiers')->nullable(); // Store directly for flexibility, fallback to plot.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};
