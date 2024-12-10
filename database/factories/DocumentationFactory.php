<?php

namespace Database\Factories;

use App\Models\Documentation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentationFactory extends Factory
{
    protected $model = Documentation::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'category' => $this->faker->randomElement(['guides', 'tutorials', 'api']),
            'order' => $this->faker->numberBetween(1, 100),
            'is_published' => true,
        ];
    }

    public function unpublished()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => false,
            ];
        });
    }
}
