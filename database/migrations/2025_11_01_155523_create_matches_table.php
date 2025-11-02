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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // 'file' or 'url'
            $table->string('status')->default('processing'); // processing, completed, failed
            $table->text('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->text('description')->nullable();
            $table->string('tags')->nullable();
            $table->string('duration')->nullable();
            $table->string('file_size')->nullable();
            $table->text('analysis')->nullable();
            $table->string('storage_disk')->default('public'); // Store which disk was used for storage (public, s3, etc.)
            $table->timestamps();
            $table->softDeletes(); // Add soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
