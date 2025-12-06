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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('conversation_service_id')->nullable()->after('conversation_id');
            $table->foreign('conversation_service_id')->references('id')->on('services')->onDelete('set null');
            $table->index('conversation_service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_service_id']);
            $table->dropColumn('conversation_service_id');
        });
    }
};
