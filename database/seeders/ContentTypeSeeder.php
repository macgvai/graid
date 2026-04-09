<?php

namespace Database\Seeders;

use App\Enums\PostType;
use App\Models\ContentType;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (PostType::cases() as $postType) {
            ContentType::query()->updateOrCreate(
                ['id' => $postType->value],
                [
                    'name' => $postType->label(),
                    'icon_class' => $postType->iconClass(),
                ],
            );
        }
    }
}
