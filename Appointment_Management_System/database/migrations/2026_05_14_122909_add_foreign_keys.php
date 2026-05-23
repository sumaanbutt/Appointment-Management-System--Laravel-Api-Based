<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        Schema::table('users', function (Blueprint $table) {

            $table->foreign('organization_code')
                ->references('code')
                ->on('organizations');

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');
        });

        /*
        |--------------------------------------------------------------------------
        | BUSINESSES
        |--------------------------------------------------------------------------
        */
        Schema::table('businesses', function (Blueprint $table) {

            $table->foreign('organization_code')
                ->references('code')
                ->on('organizations');
        });

        /*
        |--------------------------------------------------------------------------
        | BUSINESS LOCATIONS
        |--------------------------------------------------------------------------
        */
        Schema::table('business_locations', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');
        });

        /*
        |--------------------------------------------------------------------------
        | USER SHIFT SCHEDULES
        |--------------------------------------------------------------------------
        */
        Schema::table('user_shift_schedules', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('user_code')
                ->references('code')
                ->on('users');

            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations');
        });

        /*
        |--------------------------------------------------------------------------
        | CLIENTS
        |--------------------------------------------------------------------------
        */
        Schema::table('clients', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('user_code')
                ->references('code')
                ->on('users');
        });

        /*
        |--------------------------------------------------------------------------
        | SERVICES
        |--------------------------------------------------------------------------
        */
        Schema::table('services', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations');
        });

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENTS
        |--------------------------------------------------------------------------
        */
        Schema::table('appointments', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('client_code')
                ->references('code')
                ->on('clients');

            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations');

            $table->foreign('service_code')
                ->references('code')
                ->on('services');

            //$table->foreign('availability_slot_code')->references('code')->on('availability_slots');
        });

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENT SERVICES
        |--------------------------------------------------------------------------
        */
        Schema::table('appointment_services', function (Blueprint $table) {

            $table->foreign('appointment_code')
                ->references('code')
                ->on('appointments');

            $table->foreign('service_code')
                ->references('code')
                ->on('services');
        });

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENT PARTICIPANTS
        |--------------------------------------------------------------------------
        */
        Schema::table('appointment_participants', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('appointment_code')
                ->references('code')
                ->on('appointments');

            $table->foreign('user_code')
                ->references('code')
                ->on('users');
        });

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENT RECURRENCES
        |--------------------------------------------------------------------------
        */
        Schema::table('appointment_recurrences', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations');

            $table->foreign('service_code')
                ->references('code')
                ->on('services');
        });

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENT CHARGES
        |--------------------------------------------------------------------------
        */
        Schema::table('appointment_charges', function (Blueprint $table) {

            $table->foreign('appointment_code')
                ->references('code')
                ->on('appointments');

            $table->foreign('charge_code')
                ->references('code')
                ->on('charges');
        });

        /*
        |--------------------------------------------------------------------------
        | LOCATION SERVICES
        |--------------------------------------------------------------------------
        */
        Schema::table('location_services', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('location_code')
                ->references('code')
                ->on('business_locations');

            $table->foreign('service_code')
                ->references('code')
                ->on('services');
        });

        /*
        |--------------------------------------------------------------------------
        | USER ABILITIES
        |--------------------------------------------------------------------------
        */
        Schema::table('user_abilities', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('user_code')
                ->references('code')
                ->on('users');

            $table->foreign('added_by_code')
                ->references('code')
                ->on('users');

            $table->foreign('updated_by_code')
                ->references('code')
                ->on('users');
        });

        /*
        |--------------------------------------------------------------------------
        | INVOICES
        |--------------------------------------------------------------------------
        */
        Schema::table('invoices', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');

            $table->foreign('appointment_code')
                ->references('code')
                ->on('appointments');
        });

        /*
        |--------------------------------------------------------------------------
        | CHARGES
        |--------------------------------------------------------------------------
        */
        Schema::table('charges', function (Blueprint $table) {

            $table->foreign('business_code')
                ->references('code')
                ->on('businesses');
        });
    }

    public function down(): void
    {
        //
    }
};
