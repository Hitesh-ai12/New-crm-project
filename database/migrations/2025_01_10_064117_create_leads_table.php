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
            $table->string('house_number')->nullable();
            $table->string('street')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code')->nullable();

            $table->json('tasks')->nullable();
            $table->json('appointments')->nullable();

            // Additional social and personal fields
            $table->text('notes')->nullable();
            $table->date('dob')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('twitter')->nullable();

            //----
            $table->string('gender')->nullable();
            $table->string('status')->nullable();
            $table->string('company')->nullable();
            $table->string('lead_source')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('interested_in')->nullable();
            $table->string('preferred_contact_time')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('source')->nullable();
            $table->string('referred_by')->nullable();
            $table->string('marital_status')->nullable();
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('leads'); 
    }
};
