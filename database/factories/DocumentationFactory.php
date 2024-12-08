<?php

namespace Database\Factories;

use App\Models\Documentation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DocumentationFactory extends Factory
{
    protected $model = Documentation::class;

    public function definition(): array
    {
        $title = $this->faker->sentence;

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => '# ' . $title . "\n\n" . implode("\n\n", $this->faker->paragraphs(3)),
            'category' => $this->faker->randomElement(['guides', 'tutorials', 'api', 'examples']),
            'order' => $this->faker->numberBetween(1, 100),
            'is_published' => true,
            'meta_data' => null,
        ];
    }

    public function unpublished(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => false,
            ];
        });
    }
}
