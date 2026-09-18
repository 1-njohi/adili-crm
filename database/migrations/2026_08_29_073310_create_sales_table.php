<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();

            // Price & Payment
            $table->decimal('total_price', 15, 2);
            $table->decimal('deposit', 15, 2);
            $table->integer('months');
            $table->decimal('monthly_payment', 15, 2);
            $table->decimal('remaining_balance', 15, 2)->nullable();

            // Payment tier selected
            $table->string('payment_tier_selected')->nullable();

            // Commission snapshot (frozen at sale creation)
            $table->decimal('commission_rate', 10, 2)->nullable();
            $table->enum('commission_type', ['percentage', 'flat'])->nullable();

            // Status
            $table->enum('status', ['pending', 'active', 'completed', 'defaulted', 'cancelled'])
                ->default('pending');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['plot_id', 'status']);
            $table->index(['buyer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
