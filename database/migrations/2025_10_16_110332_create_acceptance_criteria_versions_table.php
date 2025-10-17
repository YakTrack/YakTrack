<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcceptanceCriteriaVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('acceptance_criteria_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('acceptance_criteria_id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('version_number');
            $table->timestamp('changed_at');
            $table->unsignedInteger('changed_by_user_id');
            $table->timestamps();

            $table->foreign('acceptance_criteria_id')->references('id')->on('acceptance_criteria')->onDelete('cascade');
            $table->foreign('changed_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['acceptance_criteria_id', 'version_number'], 'acv_criteria_version_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('acceptance_criteria_versions');
    }
}