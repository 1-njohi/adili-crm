<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soft_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_tether_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_visit_id')->constrained()->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->enum('status', ['active', 'converted', 'expired'])->default('active');
            $table->timestamps();

            $table->index(['plot_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soft_holds');
    }
};
