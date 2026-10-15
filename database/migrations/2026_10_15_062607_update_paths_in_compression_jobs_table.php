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
        Schema::table('compression_jobs', function (Blueprint $table) {
            $table->text('original_path')->change();
            $table->text('compressed_path')->nullable()->change();
            $table->text('error_message')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compression_jobs', function (Blueprint $table) {
            $table->string('original_path', 255)->change();
            $table->string('compressed_path', 255)->nullable()->change();
            $table->string('error_message', 255)->nullable()->change();
        });
    }
};
