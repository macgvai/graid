<?php

namespace Database\Factories;

use App\Enums\PostType;
use App\Models\ContentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentType>
 */
class ContentTypeFactory extends Factory
{
    public function definition(): array
    {
        $postType = fake()->randomElement(PostType::cases());

        return [
            'name' => $postType->label(),
            'icon_class' => $postType->iconClass(),
        ];
    }
}
