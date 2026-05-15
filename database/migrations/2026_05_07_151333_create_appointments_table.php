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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 9)->unique();
            $table->string('business_code', 9)->index();
            //$table->foreign('business_code')->references('code')->on('businesses');
            $table->string('location_code', 9)->index();
            //$table->foreign('location_code')->references('code')->on('business_locations');
            $table->string('client_code', 9)->index();
            //$table->foreign('client_code')->references('code')->on('clients');
            $table->string('service_code', 9)->index();
            //$table->foreign('service_code')->references('code')->on('services');
//            $table->string('availability_slot_code', 9)->nullable()->index();
//            $table->foreign('availability_slot_code')->references('code')->on('availability_slots');

            $table->date('appointment_start_date');
            $table->date('appointment_end_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', [
                'PENDING',
                'APPROVED',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED',
                'REJECTED',
                'RESCHEDULED'
            ])->default('PENDING');

            $table->string('created_by_code', 8);
            $table->string('approved_by_code', 8)->nullable();
            $table->string('cancelled_by_code', 8)->nullable();
            $table->string('rescheduled_from_code', 8)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
