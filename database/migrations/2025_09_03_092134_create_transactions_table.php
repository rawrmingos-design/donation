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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations');
            $table->foreignId('channel_id')->nullable()->constrained('payment_channels');
            $table->string('ref_id', 255);
            $table->text('instruction')->nullable();
            $table->bigInteger('total_paid');
            $table->bigInteger('total_received')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
            $table->timestamp('paid_at')->nullable();
            
            // Add indexes for better query performance
            $table->index(['status', 'created_at']);
            $table->index(['ref_id']);
            $table->index(['donation_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
