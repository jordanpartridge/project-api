<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Documentation;

class ImportDocsCommand extends Command
{
    protected $signature = 'docs:import 
        {source : Path to source JSON file or directory}
        {--category= : Documentation category}
        {--force : Force import even if docs exist}';

    protected $description = 'Import documentation from JSON files into the database';

    public function handle()
    {
        $source = $this->argument('source');
        $category = $this->option('category') ?? 'general';
        $force = $this->option('force');

        if (is_file($source)) {
            $this->importFile($source, $category);
        } elseif (is_dir($source)) {
            $this->importDirectory($source, $category);
        } else {
            $this->error("Source not found: {$source}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function importFile($file, $category)
    {
        $content = json_decode(file_get_contents($file), true);
        
        if (!isset($content['conversation'])) {
            $this->error("Invalid documentation format in {$file}");
            return;
        }

        $topics = $content['conversation']['main_topics'] ?? [];
        $useCase = $content['conversation']['specific_use_cases'] ?? [];
        
        foreach ($topics as $topic) {
            $this->createDocEntry([
                'title' => $topic['topic'],
                'content' => json_encode($topic['details']),
                'category' => $category,
                'meta_data' => [
                    'type' => 'topic',
                    'source_file' => basename($file)
                ]
            ]);
        }

        foreach ($useCase as $case) {
            $this->createDocEntry([
                'title' => $case['title'],
                'content' => json_encode($case),
                'category' => $category,
                'meta_data' => [
                    'type' => 'use_case',
                    'source_file' => basename($file)
                ]
            ]);
        }

        $this->info("Successfully imported documentation from {$file}");
    }

    protected function importDirectory($dir, $category)
    {
        $files = glob($dir . '/*.json');
        foreach ($files as $file) {
            $this->importFile($file, $category);
        }
    }

    protected function createDocEntry(array $data)
    {
        Documentation::create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'content' => $data['content'],
            'category' => $data['category'],
            'meta_data' => $data['meta_data'],
            'order' => Documentation::where('category', $data['category'])->max('order') + 1
        ]);
    }
}