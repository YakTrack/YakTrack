<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('focused_client_id')->nullable()->after('email');
            $table->foreign('focused_client_id')->references('id')->on('clients')->nullOnDelete();
            $table->index('focused_client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['focused_client_id']);
            $table->dropIndex(['focused_client_id']);
            $table->dropColumn('focused_client_id');
        });
    }
};
