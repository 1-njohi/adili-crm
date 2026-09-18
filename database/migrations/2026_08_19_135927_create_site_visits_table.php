<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booked_by_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('attending_owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('lead_tether_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained()->nullOnDelete(); // Soft hold target
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->integer('buyer_attendee_count')->default(1);
            $table->enum('status', ['pending_owner_confirmation', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending_owner_confirmation');
            $table->json('checklist')->nullable(); // [{item: 'Bring brochures', checked: true}, ...]
            $table->json('feedback')->nullable(); // {enthusiasm: 8, concern: 'Price', next_steps: 'Send offer'}
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'scheduled_at']);
            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
