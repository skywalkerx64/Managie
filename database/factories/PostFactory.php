<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Secteur;
use Illuminate\Support\Arr;
use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'post_category_id' => rand(1, PostCategory::count()),
            'secteur_id' => rand(1, Secteur::count()),
            'type' => Arr::random(Post::TYPES),
            'tags' => [fake()->word(), fake()->word(), fake()->word()],
            'created_at' => now()
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Post $complaint) {
            $complaint->setStatus(Arr::random(Post::STATUSES));
        });
    }
}
