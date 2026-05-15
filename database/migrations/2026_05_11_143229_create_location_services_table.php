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
        Schema::create('location_services', function (Blueprint $table) {
            $table->id();
            $table->string('business_code')->index();
            //$table->foreign('business_code')->references('code')->on('businesses');
            $table->string('location_code')->index();
            //$table->foreign('location_code')->references('code')->on('business_locations');
            $table->string('service_code')->index();
            //$table->foreign('service_code')->references('code')->on('services');

            $table->enum('availability', [
                'AVAILABLE',
                'NOT_AVAILABLE'
            ])->default('AVAILABLE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_services');
    }
};
