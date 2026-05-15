<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_shift_schedules', function (Blueprint $table) {
            $table->id();
            //$table->string('code', 9)->unique();
            $table->string('business_code', 9)->index();
            //$table->foreign('business_code')->references('code')->on('businesses');
            $table->string('user_code', 9)->index()->unique();
            //$table->foreign('user_code')->references('code')->on('users');
            $table->string('location_code', 9)->index()->unique();
            //$table->foreign('location_code')->references('code')->on('business_locations');

            $table->enum('employee_type', [
                'PERMANENT',
                'VISITING',
                'REMOTE'
            ]);

            $table->enum('working_day', [
                'MONDAY',
                'TUESDAY',
                'WEDNESDAY',
                'THURSDAY',
                'FRIDAY',
                'SATURDAY',
                'SUNDAY'
            ])->unique();

            $table->time('shift_start_time')->unique();
            $table->time('shift_end_time')->unique();

            $table->boolean('is_available')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_shifts');
    }
};
