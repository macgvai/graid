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
            $contentType = ContentType::query()->find($postType->value);

            if ($contentType === null) {
                $contentType = new ContentType();
                $contentType->id = $postType->value;
            }

            $contentType->name = $postType->label();
            $contentType->icon_class = $postType->iconClass();
            $contentType->save();
        }
    }
}
