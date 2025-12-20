<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tukang_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tukang_id')->constrained('tukangs')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->decimal('custom_rate', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tukang_services');
    }
};