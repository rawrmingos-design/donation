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
        Schema::table('payment_channels', function (Blueprint $table) {
            $table->decimal('fee_fixed', 10, 2)->default(0)->after('name');
            $table->decimal('fee_percentage', 10, 2)->default(0)->after('fee_fixed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_channels', function (Blueprint $table) {
            $table->dropColumn('fee');
        });
    }
};
