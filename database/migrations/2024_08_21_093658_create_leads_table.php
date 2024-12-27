<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('full_name'); // Full name of the lead
            $table->string('email'); // Email address of the lead
            $table->string('phone'); // Phone number of the lead
            $table->string('activity')->nullable(); // Optional activity field
            $table->string('source'); // Source field to store the lead's source (e.g., 'direct', 'referral')
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads'); // Drop the leads table if the migration is rolled back
    }
};
