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
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->string('contract_type')->nullable()->after('region'); // adjust position as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->dropColumn('contract_type');
        });
    }
};