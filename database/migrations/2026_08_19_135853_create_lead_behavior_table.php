<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_behavior', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_tether_id')->constrained()->cascadeOnDelete();
            $table->string('event_type'); // page_view, video_view, scroll_depth, calculator_use, button_click, exit
            $table->json('event_data')->nullable(); // URL, video_id, scroll%, calculator_inputs, etc.
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('device_os')->nullable(); // iOS, Android, Windows, macOS
            $table->string('browser')->nullable(); // Chrome, Safari, Firefox
            $table->string('location_country')->nullable();
            $table->string('location_city')->nullable();
            $table->string('timezone')->nullable();
            $table->string('ip_hash')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index(['lead_tether_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_behavior');
    }
};
