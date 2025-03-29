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
        Schema::create('job_urls', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('keyword_id'); // Foreign key to keywords
            $table->unsignedBigInteger('source_id'); // Foreign key to sources
            $table->text('url'); // Full URL for the paginated page
            $table->unsignedInteger('page'); // Page number (e.g., 1, 2, etc.)
            $table->boolean('is_scraped')->default(false); // Whether this page has been scraped
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('keyword_id')->references('id')->on('keywords')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_urls');
    }
};
