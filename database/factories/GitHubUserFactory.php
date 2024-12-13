<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GitHubUser;

class GitHubUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GitHubUser::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'github_id' => $this->faker->word(),
            'username' => $this->faker->userName(),
            'avatar_url' => $this->faker->word(),
            'profile_url' => $this->faker->word(),
            'type' => $this->faker->randomElement(["User","Organization"]),
            'repos' => $this->faker->word(),
            'projects' => $this->faker->word(),
        ];
    }
}
