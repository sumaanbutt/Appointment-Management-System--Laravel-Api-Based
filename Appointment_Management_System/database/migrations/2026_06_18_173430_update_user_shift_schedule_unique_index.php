<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            // Drop FK first
            $table->dropForeign(
                'user_shift_schedules_user_code_foreign'
            );

            // Drop old unique index
            $table->dropUnique(
                'user_shift_schedules_user_code_working_day_unique'
            );

            // Create normal index for FK support
            $table->index(
                'user_code',
                'uss_user_code_index'
            );

            // Recreate FK
            $table->foreign('user_code')
                ->references('code')
                ->on('users')
                ->cascadeOnDelete();

            // New unique constraint
            $table->unique([
                'business_code',
                'user_code',
                'location_code',
                'working_day',
                'shift_start_time',
                'shift_end_time'
            ], 'user_shift_schedule_full_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            // Remove new unique
            $table->dropUnique(
                'user_shift_schedule_full_unique'
            );

            // Drop FK again
            $table->dropForeign(
                'user_shift_schedules_user_code_foreign'
            );

            // Remove normal index
            $table->dropIndex(
                'uss_user_code_index'
            );

            // Recreate old unique
            $table->unique([
                'user_code',
                'working_day'
            ], 'user_shift_schedules_user_code_working_day_unique');

            // Recreate FK
            $table->foreign('user_code')
                ->references('code')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
