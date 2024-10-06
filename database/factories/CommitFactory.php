<?php

namespace Database\Factories;

use App\Models\Commit;
use App\Models\Repo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commit>
 */
class CommitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sha' => fake()->sha1(),
            'message' => fake()->sentence(),
            'author' => fake()->name(),
            'repo_id' => Repo::factory(),
        ];
    }
}
