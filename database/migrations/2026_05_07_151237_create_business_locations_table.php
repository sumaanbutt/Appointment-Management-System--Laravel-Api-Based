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
        Schema::create('business_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 9)->unique();
            $table->string('business_code', 9)->index();
            //$table->foreign('business_code')->references('code')->on('businesses');

            $table->enum('location_type', [
                'BUSINESS',
                'CLIENT'
            ]);

            $table->string('location_name');

            $table->text('address')->nullable();
            $table->string('street')->nullable();
            $table->string('apartment')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_locations');
    }
};
