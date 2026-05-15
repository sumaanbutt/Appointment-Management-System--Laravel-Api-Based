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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();

            $table->string('code', 9)->unique();
            $table->string('business_code', 9)->index();

            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('charge_uom', [
                'FIXED',
                'PERCENTAGE'
            ]);

            $table->decimal('charge_value', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
