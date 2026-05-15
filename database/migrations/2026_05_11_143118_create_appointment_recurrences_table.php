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
        Schema::create('appointment_recurrences', function (Blueprint $table) {
            $table->id();
            $table->string('business_code')->index();
            $table->string('service_code')->index();
            $table->string('location_code')->index();

            $table->enum('recurrence_uom', [
                'DAILY',
                'WEEKLY',
                'FORTNIGHTLY',
                'MONTHLY',
                'QUARTERLY',
                'FIXED'
            ]);

            $table->integer('recurrence_value');
            $table->integer('auto_cancel_after_days')->nullable();
            $table->integer('reschedule_after_days')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_recurrences');
    }
};
