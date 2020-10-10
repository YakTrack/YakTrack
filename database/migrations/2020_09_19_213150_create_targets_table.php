<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('targetable_type')->nullable();
            $table->string('targetable_id')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->string('duration_unit')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->string('value_unit');
            $table->unsignedInteger('value');
            $table->boolean('billable_only');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('targets');
    }
}
