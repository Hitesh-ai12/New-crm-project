<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subject')->nullable();
            $table->longText('content');
            $table->enum('type', ['email', 'sms', 'whatsapp']);
            $table->unsignedBigInteger('folder_id');
            $table->unsignedBigInteger('user_id');
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->foreign('folder_id')->references('id')->on('signature_folders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_templates');
    }
};
