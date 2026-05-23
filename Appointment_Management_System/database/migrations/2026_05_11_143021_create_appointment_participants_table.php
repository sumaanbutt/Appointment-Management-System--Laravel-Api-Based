<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_participants', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code', 9)->index()->unique();
            //$table->foreign('appointment_code')->references('code')->on('appointments');
            $table->string('business_code', 9)->index();
            //$table->foreign('business_code')->references('code')->on('businesses');
            $table->string('user_code', 9)->index()->unique();
            //$table->foreign('user_code')->references('code')->on('users');

            $table->enum('role', [
                'CLIENT',
                'STAFF',
                'ASSISTANT',
                'VISITOR'
            ])->nullable();

            $table->enum('status',[
                'ACTIVE',
                'INACTIVE',
            ])->default('ACTIVE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_participants');
    }
};
