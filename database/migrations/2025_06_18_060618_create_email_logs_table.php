<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('to');
            $table->string('from');
            $table->string('subject');
            $table->text('message');
            $table->string('direction')->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            // Optional foreign key constraints (remove if not needed)
            // $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
