<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('test_run_id');
            $table->unsignedInteger('acceptance_criteria_id');
            $table->unsignedInteger('acceptance_criteria_version_id')->nullable();
            $table->enum('status', ['passed', 'failed', 'skipped', 'blocked']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('test_run_id')->references('id')->on('test_runs')->onDelete('cascade');
            $table->foreign('acceptance_criteria_id')->references('id')->on('acceptance_criteria')->onDelete('cascade');
            $table->foreign('acceptance_criteria_version_id')->references('id')->on('acceptance_criteria_versions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
}