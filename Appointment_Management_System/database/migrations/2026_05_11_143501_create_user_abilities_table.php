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
        Schema::create('user_abilities', function (Blueprint $table) {
            $table->id();
            $table->string('business_code', 9)->index();
            //$table->foreign('business_code')->references('code')->on('businesses');
            $table->string('user_code', 9)->index()->unique();
            //$table->foreign('user_code')->references('code')->on('users');

            $table->string('ability')->unique();

            $table->enum('status', [
                'ACTIVE',
                'INACTIVE'
            ]);

            $table->string('added_by_code', 9)->nullable();
            //$table->foreign('added_by_code')->references('code')->on('users');
            $table->string('updated_by_code', 9)->nullable();
            //$table->foreign('updated_by_code')->references('code')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_abilities');
    }
};
