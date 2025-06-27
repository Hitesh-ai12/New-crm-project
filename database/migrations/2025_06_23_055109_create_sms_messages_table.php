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
    Schema::create('sms_messages', function (Blueprint $table) {

        $table->id();

        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); 
        
        $table->foreignId('lead_id')->nullable()->constrained()->onDelete('set null'); 
        $table->string('from');
        $table->string('to');
        $table->text('message');
        $table->enum('type', ['sent', 'received'])->index();
        $table->enum('delivery_status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
        $table->unsignedBigInteger('reply_to_id')->nullable(); 
        $table->json('webhook_payload')->nullable(); 
        $table->timestamp('timestamp')->nullable();
        $table->timestamps();
        $table->foreign('reply_to_id')->references('id')->on('sms_messages')->onDelete('set null');

    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
