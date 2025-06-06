<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('column_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); 
            $table->json('selected_columns');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('column_settings');
    }
};
