<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_repository', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('repository_id')->constrained('repos')->onDelete('cascade');
            $table->timestamps();
            $table->primary(['project_id', 'repository_id']);
        });

        // Only attempt to copy relationships if the repo_id column exists
        if (Schema::hasColumn('projects', 'repo_id')) {
            // Copy existing relationships to the pivot table
            DB::table('projects')
                ->whereNotNull('repo_id')
                ->select('id as project_id', 'repo_id as repository_id')
                ->orderBy('id')
                ->chunk(100, function ($projects) {
                    $now = now();
                    $records = $projects->map(function ($project) use ($now) {
                        return [
                            'project_id' => $project->project_id,
                            'repository_id' => $project->repository_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    })->toArray();
                    
                    if (!empty($records)) {
                        DB::table('project_repository')->insert($records);
                    }
                });

            // Only drop the column if it exists
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeignId('projects_repo_id_foreign');
                $table->dropColumn('repo_id');
            });
        }
    }

    public function down(): void
    {
        // Add repo_id back to projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('repo_id')->nullable()->constrained('repos')->nullOnDelete();
        });

        // Only attempt to restore relationships if the pivot table exists
        if (Schema::hasTable('project_repository')) {
            // Restore the first repository relationship for each project
            DB::table('project_repository')
                ->select('project_id', 'repository_id')
                ->orderBy('project_id')
                ->orderBy('created_at')
                ->get()
                ->groupBy('project_id')
                ->each(function ($repos, $projectId) {
                    $firstRepo = $repos->first();
                    if ($firstRepo) {
                        DB::table('projects')
                            ->where('id', $projectId)
                            ->update(['repo_id' => $firstRepo->repository_id]);
                    }
                });
        }

        Schema::dropIfExists('project_repository');
    }
};