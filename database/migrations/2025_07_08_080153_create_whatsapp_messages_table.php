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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // 👈 Add this line
            $table->unsignedBigInteger('lead_id')->nullable(); // reference to leads
            $table->string('phone');
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->text('message');
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // (optional) Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
