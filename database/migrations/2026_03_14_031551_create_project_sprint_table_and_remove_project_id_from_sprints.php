<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_sprint', function (Blueprint $table) {
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('sprint_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('sprint_id')->references('id')->on('sprints')->onDelete('cascade');
            $table->primary(['project_id', 'sprint_id']);
        });

        foreach (DB::table('sprints')->get() as $sprint) {
            DB::table('project_sprint')->insert([
                'project_id' => $sprint->project_id,
                'sprint_id'  => $sprint->id,
            ]);
        }

        Schema::table('sprints', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sprints', function (Blueprint $table) {
            $table->unsignedInteger('project_id')->after('id');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        foreach (DB::table('sprints')->get() as $sprint) {
            $first = DB::table('project_sprint')->where('sprint_id', $sprint->id)->first();
            if ($first) {
                DB::table('sprints')->where('id', $sprint->id)->update(['project_id' => $first->project_id]);
            }
        }

        Schema::dropIfExists('project_sprint');
    }
};
