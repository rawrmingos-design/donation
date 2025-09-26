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
        Schema::table('transactions', function (Blueprint $table) {
            // Midtrans specific fields
            $table->string('snap_token')->nullable()->after('ref_id');
            $table->string('payment_url', 500)->nullable()->after('snap_token');
            $table->text('qr_code')->nullable()->after('payment_url');
            $table->json('provider_response')->nullable()->after('instruction');
            
            // Additional payment fields
            $table->bigInteger('amount')->nullable()->after('total_paid'); 
            $table->bigInteger('fee_amount')->default(0)->after('amount');
            $table->string('payment_type', 50)->nullable()->after('fee_amount');
            $table->string('fraud_status', 20)->nullable()->after('payment_type');
            $table->timestamp('settlement_time')->nullable()->after('paid_at');
            $table->timestamp('expired_at')->nullable()->after('settlement_time');
            
            // Update existing columns
            $table->string('status', 20)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token',
                'payment_url',
                'qr_code',
                'provider_response',
                'amount',
                'fee_amount',
                'payment_type',
                'fraud_status',
                'settlement_time',
                'expired_at'
            ]);
        });
    }
};
