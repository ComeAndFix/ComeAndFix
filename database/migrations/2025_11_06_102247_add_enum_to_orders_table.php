<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing status column if it exists
            $table->dropColumn('status');

            // Add the new status column with enum constraints
            $table->enum('status', [
                'pending',
                'accepted',
                'on_progress',
                'rejected',
                'completed'
            ])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('status')->default('pending');
        });
    }
};
