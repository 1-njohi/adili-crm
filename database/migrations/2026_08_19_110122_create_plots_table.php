<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->integer('plot_number');
            $table->string('size')->nullable();
            $table->string('size_unit')->default('acres');
            $table->json('payment_tiers')->nullable();
            $table->json('custom_attributes')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();
            $table->softDeletes();

            // ✅ Only once
            $table->unique(['project_id', 'plot_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};