<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('transaction_id');
            $table->text('qris_url')->nullable()->after('snap_token');
            $table->timestamp('expired_at')->nullable()->after('qris_url');
            $table->text('payment_response')->nullable()->after('expired_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'qris_url', 'expired_at', 'payment_response']);
        });
    }
};
