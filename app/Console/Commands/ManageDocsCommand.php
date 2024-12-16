<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Documentation;
use Illuminate\Support\Str;

class ManageDocsCommand extends Command
{
    protected $signature = 'docs:manage
        {action : Action to perform (list|show|update|delete|reorder)}
        {--id= : Documentation ID for show/update/delete}
        {--category= : Filter or set category}
        {--title= : Set documentation title}
        {--content= : Set documentation content}
        {--order= : Set documentation order}
        {--published= : Set published status}';

    protected $description = 'Manage documentation entries in the database';

    public function handle()
    {
        $action = $this->argument('action');

        return match ($action) {
            'list' => $this->listDocs(),
            'show' => $this->showDoc(),
            'update' => $this->updateDoc(),
            'delete' => $this->deleteDoc(),
            'reorder' => $this->reorderDocs(),
            default => $this->error("Unknown action: {$action}")
        };
    }

    protected function listDocs()
    {
        $query = Documentation::query();
        
        if ($category = $this->option('category')) {
            $query->where('category', $category);
        }

        $docs = $query->orderBy('category')
                     ->orderBy('order')
                     ->get()
                     ->map(fn($doc) => [
                         'id' => $doc->id,
                         'title' => $doc->title,
                         'category' => $doc->category,
                         'order' => $doc->order,
                         'published' => $doc->is_published ? 'Yes' : 'No'
                     ]);

        $this->table(
            ['ID', 'Title', 'Category', 'Order', 'Published'],
            $docs
        );

        return Command::SUCCESS;
    }

    protected function showDoc()
    {
        if (!$id = $this->option('id')) {
            $this->error('ID is required for show action');
            return Command::FAILURE;
        }

        $doc = Documentation::find($id);
        if (!$doc) {
            $this->error("Documentation not found: {$id}");
            return Command::FAILURE;
        }

        $this->info("Title: {$doc->title}");
        $this->info("Category: {$doc->category}");
        $this->info("Order: {$doc->order}");
        $this->info("Published: " . ($doc->is_published ? 'Yes' : 'No'));
        $this->info("\nContent:");
        $this->line($doc->content);

        return Command::SUCCESS;
    }

    protected function updateDoc()
    {
        if (!$id = $this->option('id')) {
            $this->error('ID is required for update action');
            return Command::FAILURE;
        }

        $doc = Documentation::find($id);
        if (!$doc) {
            $this->error("Documentation not found: {$id}");
            return Command::FAILURE;
        }

        $updates = [];
        
        if ($title = $this->option('title')) {
            $updates['title'] = $title;
            $updates['slug'] = Str::slug($title);
        }
        
        if ($this->option('content') !== null) {
            $updates['content'] = $this->option('content');
        }
        
        if ($this->option('category') !== null) {
            $updates['category'] = $this->option('category');
        }
        
        if ($this->option('order') !== null) {
            $updates['order'] = (int)$this->option('order');
        }
        
        if ($this->option('published') !== null) {
            $updates['is_published'] = filter_var($this->option('published'), FILTER_VALIDATE_BOOLEAN);
        }

        if (empty($updates)) {
            $this->warn('No updates provided');
            return Command::SUCCESS;
        }

        $doc->update($updates);
        $this->info("Documentation updated successfully");

        return Command::SUCCESS;
    }

    protected function deleteDoc()
    {
        if (!$id = $this->option('id')) {
            $this->error('ID is required for delete action');
            return Command::FAILURE;
        }

        $doc = Documentation::find($id);
        if (!$doc) {
            $this->error("Documentation not found: {$id}");
            return Command::FAILURE;
        }

        if (!$this->confirm("Are you sure you want to delete '{$doc->title}'?")) {
            return Command::SUCCESS;
        }

        $doc->delete();
        $this->info("Documentation deleted successfully");

        return Command::SUCCESS;
    }

    protected function reorderDocs()
    {
        if (!$category = $this->option('category')) {
            $this->error('Category is required for reorder action');
            return Command::FAILURE;
        }

        $docs = Documentation::where('category', $category)
                           ->orderBy('order')
                           ->get();

        $order = 1;
        foreach ($docs as $doc) {
            $doc->update(['order' => $order++]);
        }

        $this->info("Documentation reordered successfully");
        return Command::SUCCESS;
    }
}