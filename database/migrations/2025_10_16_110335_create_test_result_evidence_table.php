<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestResultEvidenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('test_result_evidence', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('test_result_id');
            $table->enum('type', ['image', 'text']);
            $table->string('file_path')->nullable();
            $table->text('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('test_result_id')->references('id')->on('test_results')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('test_result_evidence');
    }
}
