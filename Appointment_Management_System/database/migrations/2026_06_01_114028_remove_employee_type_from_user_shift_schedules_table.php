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
        Schema::table('user_shift_schedules', function (Blueprint $table) {
            // Drop the old employee_type column completely from this table
            if (Schema::hasColumn('user_shift_schedules', 'employee_type')) {
                $table->dropColumn('employee_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_shift_schedules', function (Blueprint $table) {
            // Restore the column as a backup fallback if you roll back the migration
            $table->enum('employee_type', ['PERMANENT', 'VISITING', 'REMOTE'])->nullable();
        });
    }
};
