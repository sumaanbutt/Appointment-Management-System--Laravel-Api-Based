<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'businesses',
            function (Blueprint $table) {

                $table
                    ->string(
                        'owner_code',
                        9
                    )
                    ->nullable()
                    ->after(
                        'organization_code'
                    );

            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'businesses',
            function (Blueprint $table) {

                $table->dropColumn(
                    'owner_code'
                );

            }
        );
    }
};


