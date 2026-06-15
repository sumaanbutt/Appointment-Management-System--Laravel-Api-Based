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
        Schema::table('appointment_recurrences', function (Blueprint $table) {

            // Drop foreign keys first if they exist
            $table->dropForeign(['service_code']);
            $table->dropForeign(['location_code']);

            // Drop columns
            $table->dropColumn([
                'service_code',
                'location_code',
            ]);

            // Add appointment_code
            $table->string('appointment_code', 9)->after('business_code');

            $table->foreign('appointment_code')
                ->references('code')
                ->on('appointments')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_recurrences', function (Blueprint $table) {

            $table->dropForeign(['appointment_code']);
            $table->dropColumn('appointment_code');

            $table->string('service_code', 9)->after('business_code');
            $table->string('location_code', 9)->after('service_code');

            $table->foreign('service_code')
                ->references('code')
                ->on('services')
                ->cascadeOnDelete();

            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations')
                ->cascadeOnDelete();
        });
    }
};
