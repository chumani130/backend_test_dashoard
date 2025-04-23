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
        Schema::create('heartbeat_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->string('station')->nullable();
            $table->decimal('voltage', 8, 2)->nullable();
            $table->decimal('snr', 8, 2)->nullable();
            $table->decimal('avg_snr', 8, 2)->nullable();
            $table->decimal('rssi', 8, 2)->nullable();
            $table->unsignedInteger('seq_number')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heartbeat_data');
    }
};
