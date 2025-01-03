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
        Schema::table('service_leads', function (Blueprint $table) {
            $table->string('activity')->nullable()->after('email'); // Add column after 'email'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_leads', function (Blueprint $table) {
            $table->dropColumn('activity'); // Remove the column if rolling back
        });
    }
};
