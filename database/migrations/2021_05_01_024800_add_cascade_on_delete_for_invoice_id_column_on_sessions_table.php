<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCascadeOnDeleteForInvoiceIdColumnOnSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function ($table) {
            $table->dropForeign('sessions_invoice_id_foreign');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function ($table) {
            $table->dropForeign('sessions_invoice_id_foreign');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }
}
