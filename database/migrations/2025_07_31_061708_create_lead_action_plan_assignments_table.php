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
        Schema::create('lead_action_plan_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('action_plan_id')->constrained('automation_action_plans')->onDelete('cascade');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('current_action_id')->nullable();
            $table->timestamp('next_action_due_at')->nullable();
            $table->timestamp('assigned_at')->useCurrent(); 
            $table->timestamps();
            $table->unique(['lead_id', 'action_plan_id']);
            $table->foreign('current_action_id')->references('id')->on('automation_actions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_action_plan_assignments');
    }
};
