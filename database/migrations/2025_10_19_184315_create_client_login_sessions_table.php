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
        Schema::create('client_login_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('logged_in_at');
            $table->timestamp('logged_out_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // For storing additional session data
            $table->timestamps();
            
            $table->index(['client_user_id', 'logged_in_at']);
            $table->index(['is_active', 'logged_in_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_login_sessions');
    }
};
