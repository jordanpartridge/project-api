<?php

namespace Database\Factories;

use App\Models\PullRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PullRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PullRequest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'github_id' => $this->faker->word(),
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->text(),
            'state' => $this->faker->randomElement(['open', 'closed', 'merged']),
            'repo_id' => $this->faker->word(),
            'author_id' => $this->faker->word(),
            'merged_by_id' => $this->faker->word(),
            'project_card_id' => $this->faker->word(),
            'closed_at' => $this->faker->dateTime(),
            'merged_at' => $this->faker->dateTime(),
        ];
    }
}
