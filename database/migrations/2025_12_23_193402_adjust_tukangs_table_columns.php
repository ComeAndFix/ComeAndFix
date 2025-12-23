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
        Schema::table('tukangs', function (Blueprint $table) {
            // Drop columns as requested
            if (Schema::hasColumn('tukangs', 'hourly_rate')) {
                $table->dropColumn('hourly_rate');
            }
            if (Schema::hasColumn('tukangs', 'business_name')) {
                $table->dropColumn('business_name');
            }

            // Make location columns nullable since they are removed from registration input
            $table->text('address')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            
            // Years experience is requested to be removed from input but kept in DB
            // It already has a default(0) in original migration, but ensuring nullable is safe
            $table->integer('years_experience')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tukangs', function (Blueprint $table) {
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('business_name')->nullable();
            
            // Reverting nullable changes might fail if there are null values, 
            // but strict reversal would be to make them required again.
            // For safety in this dev environment context, simply checking nulls or leaving nullable.
        });
    }
};
