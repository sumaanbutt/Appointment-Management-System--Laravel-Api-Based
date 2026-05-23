<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            $table->dropForeign([
                'user_code'
            ]);

            $table->dropUnique([
                'user_code'
            ]);

            $table->foreign('user_code')
                ->references('code')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            $table->dropForeign([
                'user_code'
            ]);

            $table->unique('user_code');

            $table->foreign('user_code')
                ->references('code')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
