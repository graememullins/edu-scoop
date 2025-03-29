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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profession_id');
            $table->string('keyword');
            $table->datetime('last_run')->nullable();
            $table->string('status')->default('1');
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraint
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
