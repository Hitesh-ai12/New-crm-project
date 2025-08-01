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
        Schema::create('automation_action_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('pause_on_reply')->default(false); // <--- ADD THIS LINE
            $table->unsignedInteger('step_count')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->json('tracking_stats')->nullable(); // { applied: 10, active: 8, paused: 2 }
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_action_plans');
    }
};
