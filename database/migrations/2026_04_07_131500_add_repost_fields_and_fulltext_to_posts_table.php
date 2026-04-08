<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->foreignId('original_post_id')
                ->nullable()
                ->after('user_id')
                ->constrained('posts')
                ->nullOnDelete();
            $table->foreignId('original_author_id')
                ->nullable()
                ->after('original_post_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('is_repost')->default(false)->after('link_preview');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('posts', function (Blueprint $table): void {
                $table->fullText(['title', 'text_content'], 'posts_title_text_fulltext');
            });
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('posts', function (Blueprint $table): void {
                $table->dropFullText('posts_title_text_fulltext');
            });
        }

        Schema::table('posts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('original_author_id');
            $table->dropConstrainedForeignId('original_post_id');
            $table->dropColumn('is_repost');
        });
    }
};
