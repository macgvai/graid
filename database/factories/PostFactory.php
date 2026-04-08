<?php

namespace Database\Factories;

use App\Models\ContentType;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content_type_id' => ContentType::factory(),
            'title' => fake()->sentence(3),
            'text_content' => fake()->paragraph(),
            'views' => 0,
        ];
    }
}
