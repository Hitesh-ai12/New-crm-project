<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('leads'); // Drop the existing leads table
        
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('first_name'); // Required  
            $table->string('last_name');
            $table->string('email')->unique(); // Required and unique
            $table->string('phone')->unique(); // Required and unique
            $table->unsignedBigInteger('user_id'); // Foreign key for logged-in user
            $table->string('tag')->nullable();
            $table->string('stage')->nullable();           
            $table->text('new_listing_alert')->nullable();
            $table->text('lead_basic_details')->nullable();
            $table->text('neighbourhood_alert')->nullable();
            $table->text('open_house_alert')->nullable();
            $table->text('price_drop_alert')->nullable();
            $table->text('action_plans')->nullable();
            $table->json('files')->nullable();
            $table->text('background')->nullable();
            $table->text('real_estate_newsletter')->nullable();
            $table->text('market_updates')->nullable();
            $table->string('city')->nullable();
            $table->json('tasks')->nullable();
            $table->json('appointments')->nullable();
            
            $table->timestamps(); // Adds created_at and updated_at columns
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads'); // Drop the leads table if rolled back
    }
};
