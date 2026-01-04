<?php 

namespace Database\Factories;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityFactory extends Factory
{
    protected $model = Entity::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'type' => $this->faker->word,
            'metadata' => ['test' => 'data']
        ];
    }
}