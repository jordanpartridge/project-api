<?php

namespace Database\Factories;

use App\Models\ProjectColumn;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectColumnFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectColumn::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'github_id' => $this->faker->word(),
            'name' => $this->faker->name(),
            'project_id' => $this->faker->word(),
            'cards' => $this->faker->word(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
