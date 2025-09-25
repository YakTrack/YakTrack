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
        Schema::table('tasks', function (Blueprint $table) {
            // Check if column already exists (for testing scenarios)
            if (!Schema::hasColumn('tasks', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->after('description');
                $table->foreign('status_id')->references('id')->on('task_statuses')->onDelete('set null');
                $table->index('status_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
    }
};
