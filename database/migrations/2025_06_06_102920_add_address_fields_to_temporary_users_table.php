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
        Schema::table('temporary_users', function (Blueprint $table) {
            //
            $table->string('country_of_residence')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('apartment_name')->nullable();
            $table->string('room_number')->nullable();
            $table->boolean('is_expatriate')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_users', function (Blueprint $table) {
            //
            $table->dropColumn([
                'country_of_residence',
                'city',
                'postal_code',
                'apartment_name',
                'room_number',
                'is_expatriate',
            ]);
        });
    }
};
