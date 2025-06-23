<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('leads'); 
        
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); 
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique(); 
            $table->string('phone', 20)->unique(); 
            $table->unsignedBigInteger('user_id'); 
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
            $table->string('house_number')->nullable();   // ✅ added
            $table->string('street')->nullable();         // ✅ added
            $table->string('province')->nullable();       // ✅ added
            $table->string('zip_code')->nullable();       // ✅ added
            $table->json('tasks')->nullable();
            $table->json('appointments')->nullable();
            $table->timestamps(); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('leads'); 
    }
};
