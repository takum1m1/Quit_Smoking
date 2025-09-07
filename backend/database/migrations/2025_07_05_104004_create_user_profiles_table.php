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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name', 20);
            $table->unsignedInteger('daily_cigarettes');
            $table->integer('pack_cost');
            $table->date('quit_date');
            $table->integer('quit_days_count')->default(0);
            $table->integer('quit_cigarettes')->default(0);
            $table->integer('saved_money')->default(0);
            $table->integer('extended_life')->default(0);
            $table->json('badges')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
