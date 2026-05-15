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
        Schema::create('appointment_charges', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code')->index();
            //$table->foreign('appointment_code')->references('code')->on('appointments');
            $table->string('charge_code')->index();
            //$table->foreign('charge_code')->references('code')->on('charges');

            $table->decimal('charge_value', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_charges');
    }
};
