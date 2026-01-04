<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Owner;
use App\Models\Project;
use App\Models\Repo;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepoFactory extends Factory
{
    protected $model = Repo::class;

    public function definition(): array
    {
        return [
            'github_id' => (string)$this->faker->unique()->numberBetween(1000000, 9999999),
            'name' => $this->faker->name,
            'full_name' => $this->faker->name,
            'language_id' => Language::factory(),
            'url' => $this->faker->url,
            'owner_id' => Owner::factory(),
            'project_id' => Project::factory(),
            'description' => $this->faker->paragraph,
            'private' => $this->faker->boolean,
        ];
    }
}