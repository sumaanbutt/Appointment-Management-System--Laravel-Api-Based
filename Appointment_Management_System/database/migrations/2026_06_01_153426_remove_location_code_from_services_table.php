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
        Schema::table('services', function (Blueprint $table) {

            // drop foreign key first
            $table->dropForeign(
                ['location_code']
            );

            // then drop column
            $table->dropColumn(
                'location_code'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->string(
                'location_code'
            )->index();

            $table->foreign(
                'location_code'
            )
                ->references('code')
                ->on('business_locations');
        });
    }
};
