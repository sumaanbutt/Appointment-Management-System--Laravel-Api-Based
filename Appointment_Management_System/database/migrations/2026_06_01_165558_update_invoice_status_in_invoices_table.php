<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // STEP 1 — update old data

        DB::table('invoices')
            ->where('status','PENDING')
            ->update([
                'status'=>'PAID'
            ]);

        DB::table('invoices')
            ->where('status','FAILED')
            ->update([
                'status'=>'PAID'
            ]);

        DB::table('invoices')
            ->where('status','REFUNDED')
            ->update([
                'status'=>'PAID'
            ]);

        // STEP 2 — change enum

        Schema::table(
            'invoices',
            function(Blueprint $table){

                $table->enum(
                    'status',
                    [
                        'draft',
                        'paid',
                        'issued',
                        'canceled'
                    ]
                )
                    ->default('draft')
                    ->change();

            }
        );

        // STEP 3 — remap values

        DB::table('invoices')
            ->where('status','PAID')
            ->update([
                'status'=>'paid'
            ]);
    }

    public function down()
    {
        Schema::table(
            'invoices',
            function(Blueprint $table){

                $table->enum(
                    'status',
                    [
                        'PENDING',
                        'PAID',
                        'FAILED',
                        'REFUNDED'
                    ]
                )
                    ->default('PENDING')
                    ->change();

            }
        );
    }
};
