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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'sms' or 'email'
            $table->string('title');
            $table->string('subject')->nullable(); // Null for SMS templates
            $table->text('content');
            $table->string('attachment_path')->nullable(); // Used for email templates
            $table->unsignedBigInteger('created_by'); // User ID of the creator
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
