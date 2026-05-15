<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Drop Foreign Key
            |--------------------------------------------------------------------------
            */
            $table->dropForeign([
                'location_code'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Drop Unique
            |--------------------------------------------------------------------------
            */
            $table->dropUnique([
                'location_code'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Add Foreign Key Again
            |--------------------------------------------------------------------------
            */
            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            $table->dropForeign([
                'location_code'
            ]);

            $table->unique('location_code');

            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations')
                ->onDelete('cascade');
        });
    }
};
