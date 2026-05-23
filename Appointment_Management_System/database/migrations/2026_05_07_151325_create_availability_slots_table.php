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
        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->string('code', 9)->unique();
            $table->string('service_code')->index();
            $table->string('location_code')->index();

            $table->date('slot_date');

            $table->time('start_time');
            $table->time('end_time');

            $table->integer('maximum_bookings')->default(1);
            $table->integer('current_bookings')->default(0);

            $table->boolean('is_available')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_slots');
    }
};
