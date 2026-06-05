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
        Schema::table('appointment_participants', function (Blueprint $table) {

            $table->renameColumn('role', 'user_role');

            $table->string('user_type')
                ->nullable()
                ->after('user_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_participants', function (Blueprint $table) {

            $table->dropColumn('user_type');

            $table->renameColumn('user_role', 'role');
        });
    }
};
