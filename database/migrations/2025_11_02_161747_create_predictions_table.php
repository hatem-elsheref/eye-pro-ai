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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->string('clip_path')->nullable(); // S3 path to the clip
            $table->string('relative_time')->nullable(); // Clip start time relative to main match video
            $table->decimal('first_model_prop', 10, 8)->nullable(); // First model accuracy (float)
            $table->json('prediction_0')->nullable(); // First model prediction with classes and accuracies
            $table->json('prediction_1')->nullable(); // Second model prediction with classes and accuracies
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
