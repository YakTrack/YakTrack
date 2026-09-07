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
        Schema::create('session_pending_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('session_id');
            $table->unsignedInteger('task_id');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete();
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
            $table->unique(['session_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_pending_tasks');
    }
};
