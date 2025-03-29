<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->renameColumn('postcode', 'post_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('post_code');
            $table->decimal('latitude', 10, 7)->nullable()->after('longitude');
            $table->boolean('post_code_validated')->default(false)->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('nhs_england_jobs', function (Blueprint $table) {
            $table->renameColumn('post_code', 'postcode');
            $table->dropColumn(['longitude', 'latitude', 'post_code_validated']);
        });
    }
};
