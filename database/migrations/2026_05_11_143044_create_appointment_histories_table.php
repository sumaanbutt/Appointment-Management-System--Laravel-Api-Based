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
        Schema::create('appointment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code')->index();
            $table->string('business_code')->index();

            $table->enum('action', [
                'CREATED',
                'UPDATED',
                'ASSIGNED',
                'RESCHEDULED',
                'CANCELLED'
            ]);

            $table->string('changed_by_code', 8);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_histories');
    }
};
