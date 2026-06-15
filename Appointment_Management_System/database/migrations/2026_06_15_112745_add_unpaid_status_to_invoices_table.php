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
        // For MySQL: Modifies the enum column to include 'UNPAID'
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status
                    ENUM(
                        'draft',
                        'paid',
                        'issued',
                        'canceled',
                        'unpaid') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverts the enum column back to its original state
        // Note: Make sure no rows are set to 'UNPAID' before rolling back, or MySQL will throw an error.
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft', 'paid', 'issued', 'canceled') DEFAULT 'draft'");
    }
};
