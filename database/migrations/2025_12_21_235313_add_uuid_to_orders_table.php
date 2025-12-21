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
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Populate existing records
        \Illuminate\Support\Facades\DB::table('orders')->get()->each(function ($order) {
            \Illuminate\Support\Facades\DB::table('orders')
                ->where('id', $order->id)
                ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
        });

        // Make it not nullable and unique after population
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
