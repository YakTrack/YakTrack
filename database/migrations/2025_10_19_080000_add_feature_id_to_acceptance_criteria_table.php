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
        Schema::table('acceptance_criteria', function (Blueprint $table) {
            $table->unsignedBigInteger('feature_id')->nullable()->after('name');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('set null');
            $table->index('feature_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acceptance_criteria', function (Blueprint $table) {
            $table->dropForeign(['feature_id']);
            $table->dropIndex(['feature_id']);
            $table->dropColumn('feature_id');
        });
    }
};
