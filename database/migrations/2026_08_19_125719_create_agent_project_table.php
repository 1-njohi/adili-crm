<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->enum('commission_type', ['percentage', 'flat'])->default('percentage');
            $table->decimal('commission_rate', 10, 2);
            $table->string('commission_currency', 3)->default('KES');
            $table->enum('status', ['active', 'inactive', 'pending_removal'])->default('active');
            $table->timestamps();

            $table->unique(['agent_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_project');
    }
};