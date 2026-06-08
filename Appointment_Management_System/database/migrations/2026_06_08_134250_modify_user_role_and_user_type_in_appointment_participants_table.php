<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE appointment_participants
            MODIFY user_type ENUM(
                'super_admin',
                'business_owner',
                'operational_staff',
                'service_staff',
                'client'
            ) NULL
        ");

        DB::statement("
            ALTER TABLE appointment_participants
            MODIFY user_role VARCHAR(255) NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE appointment_participants
            MODIFY user_role ENUM(
                'CLIENT',
                'STAFF',
                'ASSISTANT',
                'VISITOR'
            ) NULL
        ");

        DB::statement("
            ALTER TABLE appointment_participants
            MODIFY user_type VARCHAR(255) NULL
        ");
    }
};
