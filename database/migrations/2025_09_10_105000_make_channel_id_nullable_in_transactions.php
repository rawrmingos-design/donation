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
            // Make channel_id nullable for Midtrans transactions
            $table->foreignId('channel_id')->nullable()->change();
            
            // Add provider_id to directly reference payment provider
            $table->foreignId('provider_id')->nullable()->after('channel_id')->constrained('payment_providers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropColumn('provider_id');
            $table->foreignId('channel_id')->nullable(false)->change();
        });
    }
};
