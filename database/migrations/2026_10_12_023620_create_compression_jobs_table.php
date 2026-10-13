<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compression_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('original_filename');
            $table->string('original_path');
            $table->string('compressed_path')->nullable();
            $table->bigInteger('original_size'); // bytes
            $table->bigInteger('compressed_size')->nullable(); // bytes
            $table->decimal('compression_ratio', 5, 2)->nullable(); // percentage
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('error_message')->nullable();
            $table->timestamp('compressed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compression_jobs');
    }
};
