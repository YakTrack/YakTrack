<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {

            // Create tables
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->string('status');
            $table->integer('sprint_id')->unsigned()->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('parent_type');
            $table->timestamps();

            // Foreign key relationships
            $table->foreign('sprint_id')->references('id')->on('sprints');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tasks');
    }
}
