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
        Schema::create('nhs_england_jobs', function (Blueprint $table) {
            $table->string('job_id')->primary(); // Unique job ID from NHS website
            $table->string('job_link')->nullable(); // URL of the job posting
            $table->unsignedBigInteger('keyword_id')->nullable(); // Foreign key to keywords
            $table->unsignedBigInteger('source_id')->nullable(); // Foreign key to sources
            $table->string('job_title')->nullable(); // Title of the job posting
            $table->date('posted_date')->nullable(); // Date the job was posted
            $table->date('closing_date')->nullable(); // Application closing date
            $table->string('trust')->nullable(); // Trust name
            $table->string('reference_number')->nullable(); // Job reference number
            $table->string('band')->nullable(); // NHS Band/Pay grade
            $table->string('contact_job_title')->nullable(); // Contact person's job title
            $table->string('contact_name')->nullable(); // Contact person's name
            $table->string('contact_email')->nullable(); // Contact email
            $table->string('contact_phone')->nullable(); // Contact phone number
            $table->string('address_line_1')->nullable(); // Address line 1
            $table->string('address_line_2')->nullable(); // Address line 2
            $table->string('town')->nullable(); // Town
            $table->string('postcode')->nullable(); // Postcode
            $table->boolean('is_scraped')->default(false); // Whether detailed data has been scraped
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at

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
        Schema::dropIfExists('nhs_england_jobs');
    }
};