<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_tethers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('phone_hash')->index();
            $table->string('phone_encrypted')->nullable();
            $table->string('email_hash')->nullable()->index();
            $table->string('email_encrypted')->nullable();
            $table->string('name')->nullable();
            $table->string('source')->default('microsite'); // microsite, site_booking, direct_call, owner_manual
            $table->string('source_platform')->nullable(); // instagram, facebook, whatsapp, etc.
            $table->json('attribution_data')->nullable(); // ref, utm_source, etc.
            $table->string('fingerprint')->nullable();
            $table->timestamp('tethered_at')->useCurrent();
            $table->timestamp('expires_at');
            $table->boolean('attribution_conflict')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['phone_hash', 'project_id']);

            $table->unique(['agent_id', 'project_id', 'phone_hash'], 'unique_lead_tether');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_tethers');
    }
};