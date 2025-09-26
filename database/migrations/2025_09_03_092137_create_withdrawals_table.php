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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->bigInteger('amount'); // Amount in cents
            $table->bigInteger('fee_amount')->default(0); // Platform fee in cents
            $table->bigInteger('net_amount'); // Net amount after fee
            $table->string('method', 100); // bank_transfer, e_wallet, etc
            $table->json('account_info'); // Bank account details
            $table->enum('status', ['pending', 'approved', 'processing', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->text('notes')->nullable(); // Admin notes or rejection reason
            $table->string('reference_number')->nullable(); // Bank reference number
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Admin who approved
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
