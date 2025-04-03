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
        Schema::create('teaching_jobs', function (Blueprint $table) {
            $table->string('job_id')->primary(); // Unique job ID from source site
            $table->string('job_link')->nullable(); // URL of the job posting
            $table->string('external_job_slug')->nullable(); // Hash of the job link for uniqueness
            $table->unsignedBigInteger('keyword_id')->nullable(); // Foreign key to keywords
            $table->unsignedBigInteger('profession_id')->nullable(); // Foreign key to professions
            $table->unsignedBigInteger('source_id')->nullable(); // Foreign key to sources
            $table->string('job_title')->nullable(); // Title of the job posting
            $table->date('posted_date')->nullable(); // Date the job was posted
            $table->date('closing_date')->nullable(); // Application closing date
            $table->string('posted_by')->nullable(); // School name
            $table->string('subject')->nullable(); // Subject area
            $table->string('education_phase')->nullable(); // e.g., Primary, Secondary
            $table->string('age_range')->nullable(); // Age range of students
            $table->string('school_size')->nullable(); // e.g., number of pupils
            $table->string('school_type')->nullable(); // e.g., Academy, Free School
            $table->string('contract_type')->nullable(); // Permanent, Temporary
            $table->string('reference_number')->nullable(); // Job reference number
            $table->string('key_stages')->nullable(); // e.g., KS1, KS2, KS3, KS4
            $table->string('contact_job_title')->nullable(); // e.g., Headteacher
            $table->string('contact_name')->nullable(); // Contact person's name
            $table->string('contact_email')->nullable(); // Email for enquiries
            $table->string('contact_phone')->nullable(); // Phone number
            $table->decimal('latitude', 10, 8)->nullable(); // Latitude for location
            $table->decimal('longitude', 11, 8)->nullable(); // Longitude for location
            $table->string('town')->nullable();
            $table->string('nuts')->nullable();
            $table->string('pfa')->nullable();
            $table->string('region')->nullable(); // Region of the job
            $table->string('country')->nullable(); // Default to UK
            $table->string('nuts')->nullable(); // Nomenclature of Territorial Units for Statistics
            $table->string('pfa')->nullable(); // Public Funding Agency
            $table->string('post_code')->nullable(); // Postal code
            $table->boolean('is_scraped')->default(false); // Scrape status
            $table->boolean('keyword_checked')->default(false);
            $table->boolean('post_code_validated')->default(false);
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at

            // Foreign key constraints
            $table->foreign('keyword_id')->references('id')->on('keywords')->onDelete('cascade');
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_jobs');
    }
};
