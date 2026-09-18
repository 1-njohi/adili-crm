<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('land_size')->nullable(); // e.g., "2 acres"
            $table->string('land_size_unit')->default('acres');
            $table->string('status')->default('draft'); // draft, active, sold_out
            $table->json('neighbor_discount')->nullable(); // {percentage: 5, active: true}
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
