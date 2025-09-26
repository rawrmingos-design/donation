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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title', 300);
            $table->string('slug', 350)->unique();
            $table->text('short_desc')->nullable();
            $table->text('description')->nullable();
            $table->string('featured_image', 500)->nullable();
            $table->bigInteger('target_amount')->default(0);
            $table->bigInteger('collected_amount')->default(0);
            $table->string('currency', 3)->default('IDR');
            $table->string('goal_type', 50)->default('amount');
            $table->date('deadline')->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('allow_anonymous')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
