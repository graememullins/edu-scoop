<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->foreignId('profession_id')
                ->nullable()
                ->constrained('professions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->dropForeign(['profession_id']);
            $table->dropColumn('profession_id');
        });
    }
};