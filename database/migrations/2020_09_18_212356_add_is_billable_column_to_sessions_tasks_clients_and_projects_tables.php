<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBillableColumnToSessionsTasksClientsAndProjectsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->boolean('is_billable')->after('invoice_id')->default(0);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_billable')->after('parent_id')->default(0);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('is_billable')->after('email')->default(0);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_billable')->after('client_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('is_billable');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('is_billable');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_billable');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('is_billable');
        });
    }
}
