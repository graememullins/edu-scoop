<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->string('region')->nullable()->after('post_code');
            $table->string('ccg')->nullable()->after('region');
        });
    }

    public function down(): void
    {
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->dropColumn(['region', 'ccg']);
        });
    }
};