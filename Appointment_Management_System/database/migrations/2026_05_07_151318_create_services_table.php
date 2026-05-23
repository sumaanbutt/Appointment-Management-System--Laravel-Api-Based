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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 9)->unique();
            $table->string('business_code')->index();
            //$table->foreign('business_code')->references('code')->on('businesses');
            $table->string('location_code')->index();
            //$table->foreign('location_code')->references('code')->on('business_locations');

            $table->string('service_name');
            $table->text('description')->nullable();

            $table->integer('time_duration')->nullable();
            $table->decimal('charges', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('currency', 10)->default('PKR');

            $table->enum('availability', [
                'ONSITE',
                'ONLINE',
                'BOTH'
            ]);

            $table->enum('duration_uom', [
                'WEEK',
                'DAY',
                'HOUR',
                'MINUTE'
            ])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
