<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiKeysTable extends Migration
{
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();  // Auto-incrementing ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Link to the user
            $table->string('key')->unique();  // Store the API key
            $table->json('permissions');  // Store permissions as JSON (e.g., ['create', 'read'])
            $table->string('endpoint');  // Store the generated endpoint
            $table->timestamps();  // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_keys');
    }
}
