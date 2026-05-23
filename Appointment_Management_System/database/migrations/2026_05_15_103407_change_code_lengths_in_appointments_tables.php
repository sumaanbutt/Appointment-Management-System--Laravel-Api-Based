<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            $table->string('created_by_code', 9)->change();
            $table->string('approved_by_code', 9)->nullable()->change();
            $table->string('cancelled_by_code', 9)->nullable()->change();
            $table->string('rescheduled_from_code', 9)->nullable()->change();
        });

        Schema::table('appointment_histories', function (Blueprint $table) {

            $table->string('changed_by_code', 9)->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            $table->string('created_by_code', 8)->change();
            $table->string('approved_by_code', 8)->nullable()->change();
            $table->string('cancelled_by_code', 8)->nullable()->change();
            $table->string('rescheduled_from_code', 8)->nullable()->change();
        });

        Schema::table('appointment_histories', function (Blueprint $table) {

            $table->string('changed_by_code', 8)->change();
        });
    }
};
