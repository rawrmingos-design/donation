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
        Schema::table('donations', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index(['campaign_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['donor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['donor_id', 'created_at']);
        });
    }
};
