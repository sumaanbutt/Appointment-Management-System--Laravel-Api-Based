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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 9)->unique();
            $table->string('business_code', 9)->index();
            //$table->foreign('business_code')->references('code')->on('businesses');
            $table->string('appointment_code', 9)->index();
            //$table->foreign('appointment_code')->references('code')->on('appointments');

            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);

            $table->enum('status', [
                'PENDING',
                'PAID',
                'FAILED',
                'REFUNDED'
            ])->default('PENDING');

            $table->date('invoice_date');

            $table->string('updated_by_code', 9)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
