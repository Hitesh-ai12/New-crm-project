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
        Schema::create('automation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('automation_action_plans')->onDelete('cascade');
            $table->string('type');
            $table->integer('delay_days')->default(0);
            $table->string('task_name')->nullable();
            $table->string('task_type')->nullable();
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->unsignedBigInteger('sms_template_id')->nullable();
            $table->text('note_content')->nullable();
            $table->json('new_stage')->nullable();
            $table->json('add_tags')->nullable();
            $table->json('remove_tags')->nullable();
            $table->string('pause_specific_plan')->nullable();
            $table->string('assign_action_plan')->nullable();
            $table->unsignedInteger('step_order')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_actions');
    }
};
