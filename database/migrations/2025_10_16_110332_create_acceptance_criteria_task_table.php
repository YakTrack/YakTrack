<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcceptanceCriteriaTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('acceptance_criteria_task', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('acceptance_criteria_id');
            $table->unsignedInteger('task_id');
            $table->timestamps();

            $table->foreign('acceptance_criteria_id')->references('id')->on('acceptance_criteria')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->unique(['acceptance_criteria_id', 'task_id'], 'unique_criteria_task');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('acceptance_criteria_task');
    }
}