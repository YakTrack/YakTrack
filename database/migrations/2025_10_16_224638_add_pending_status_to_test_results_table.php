<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingStatusToTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->enum('status', ['pending', 'passed', 'failed', 'skipped', 'blocked'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->enum('status', ['passed', 'failed', 'skipped', 'blocked'])->change();
        });
    }
}
