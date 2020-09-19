<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalHoursClientIdIsSentDescriptionAndIsPaidColumnsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable();
            $table->float('total_hours')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->text('description')->default('');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_sent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->dropColumn('total_hours');
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
            $table->dropColumn('description');
            $table->dropColumn('is_paid');
            $table->dropColumn('is_sent');
        });
    }
}
