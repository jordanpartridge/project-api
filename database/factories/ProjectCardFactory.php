<?php

namespace Database\Factories;

use App\Models\ProjectCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectCard::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'github_id' => $this->faker->word(),
            'note' => $this->faker->text(),
            'issue_id' => $this->faker->word(),
            'pull_request_id' => $this->faker->word(),
            'column_id' => $this->faker->word(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
