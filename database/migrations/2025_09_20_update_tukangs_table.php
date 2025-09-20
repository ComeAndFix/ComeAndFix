<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tukangs', function (Blueprint $table) {
            if (!Schema::hasColumn('tukangs', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('tukangs', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('tukangs', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->nullable()->after('years_experience');
            }
            if (!Schema::hasColumn('tukangs', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('hourly_rate');
            }
            if (!Schema::hasColumn('tukangs', 'business_name')) {
                $table->string('business_name')->nullable()->after('profile_image');
            }
            if (!Schema::hasColumn('tukangs', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('business_name');
            }
            if (!Schema::hasColumn('tukangs', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0)->after('is_verified');
            }
            if (!Schema::hasColumn('tukangs', 'total_reviews')) {
                $table->integer('total_reviews')->default(0)->after('rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tukangs', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'hourly_rate', 'profile_image', 
                'business_name', 'is_verified', 'rating', 'total_reviews'
            ]);
        });
    }
};