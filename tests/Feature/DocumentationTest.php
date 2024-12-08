<?php

use App\Models\Documentation;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('index page renders', function () {
    // Arrange
    Documentation::factory()->count(3)->create();

    // Act & Assert
    $this->get('/')
        ->assertOk()
        ->assertSeeText('Project-API');
});

test('docs page shows documentation', function () {
    // Arrange
    $doc = Documentation::factory()->create([
        'title' => 'Getting Started',
        'content' => '# Getting Started Guide',
        'category' => 'guides',
    ]);

    // Act & Assert
    $this->get('/docs')
        ->assertOk()
        ->assertSee('Getting Started')
        ->assertSee('Getting Started Guide');
});

test('docs page filters by category', function () {
    // Arrange
    Documentation::factory()->create([
        'title' => 'Guide One',
        'category' => 'guides',
    ]);

    Documentation::factory()->create([
        'title' => 'Tutorial One',
        'category' => 'tutorials',
    ]);

    // Act & Assert
    livewire(\App\Livewire\Pages\Docs\Index::class)
        ->assertSee('Guide One')
        ->assertSee('Tutorial One')
        ->set('selectedCategory', 'guides')
        ->assertSee('Guide One')
        ->assertDontSee('Tutorial One');
});

test('can create new documentation', function () {
    // Act & Assert
    livewire(\App\Livewire\Pages\Docs\Settings::class)
        ->set('title', 'New Guide')
        ->set('content', '# New Guide Content')
        ->set('category', 'guides')
        ->set('order', 1)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('documentation', [
        'title' => 'New Guide',
        'category' => 'guides',
    ]);
});

test('requires title for documentation', function () {
    // Act & Assert
    livewire(\App\Livewire\Pages\Docs\Settings::class)
        ->set('content', '# Content')
        ->set('category', 'guides')
        ->set('order', 1)
        ->call('save')
        ->assertHasErrors(['title']);
});

test('unpublished docs are hidden', function () {
    // Arrange
    Documentation::factory()->create([
        'title' => 'Published Doc',
        'is_published' => true,
    ]);

    Documentation::factory()->create([
        'title' => 'Unpublished Doc',
        'is_published' => false,
    ]);
});
