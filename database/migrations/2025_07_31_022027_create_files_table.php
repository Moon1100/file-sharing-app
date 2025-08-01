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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('uploader_token')->unique();
            $table->string('original_name');
            $table->string('compressed_path');
            $table->string('pin_code', 6)->unique();
            $table->integer('downloads')->default(0);
            $table->integer('max_downloads')->default(2);
            $table->timestamp('expires_at');
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
