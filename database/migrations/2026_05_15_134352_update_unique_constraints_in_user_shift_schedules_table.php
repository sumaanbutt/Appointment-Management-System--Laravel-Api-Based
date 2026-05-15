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
            | Remove old unique from working_day
            |--------------------------------------------------------------------------
            */
            $table->dropUnique([
                'working_day'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Add composite unique
            |--------------------------------------------------------------------------
            */
            $table->unique([
                'user_code',
                'working_day'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Remove composite unique
            |--------------------------------------------------------------------------
            */
            $table->dropUnique([
                'user_code',
                'working_day'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Add back old unique
            |--------------------------------------------------------------------------
            */
            $table->unique('working_day');
        });
    }
};
