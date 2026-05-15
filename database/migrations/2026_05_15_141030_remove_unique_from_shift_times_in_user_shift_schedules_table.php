<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            $table->dropUnique([
                'shift_start_time'
            ]);

            $table->dropUnique([
                'shift_end_time'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            $table->unique('shift_start_time');

            $table->unique('shift_end_time');
        });
    }
};
