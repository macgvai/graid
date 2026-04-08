<?php

namespace Tests\Feature;

use App\Enums\PostType;
use App\Models\Hashtag;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\ContentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SearchAndFeedTest extends TestCase
{
    use RefreshDatabase;

    public function testFeedShowsOnlyPostsFromSubscribedAuthors(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $viewer = User::factory()->create();
        $subscribedAuthor = User::factory()->create();
        $otherAuthor = User::factory()->create();

        $viewer->subscriptions()->create(['target_id' => $subscribedAuthor->id]);

        Post::factory()->create([
            'user_id' => $subscribedAuthor->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Нужный пост',
        ]);
        Post::factory()->create([
            'user_id' => $otherAuthor->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Лишний пост',
        ]);

        $response = $this
            ->actingAs($viewer)
            ->get(route('feed'));

        $response->assertOk();
        $response->assertSeeText('Нужный пост');
        $response->assertDontSeeText('Лишний пост');
    }

    public function testSearchByTagReturnsRelatedPosts(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $viewer = User::factory()->create();
        $author = User::factory()->create();
        $matchingPost = Post::factory()->create([
            'user_id' => $author->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Пост про путешествия',
        ]);
        $otherPost = Post::factory()->create([
            'user_id' => $author->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Пост без тега',
        ]);
        $hashtag = Hashtag::query()->create(['name' => 'travel']);
        $matchingPost->hashtags()->attach($hashtag);

        $response = $this
            ->actingAs($viewer)
            ->get(route('search-results', ['query' => '#travel']));

        $response->assertOk();
        $response->assertSeeText('Пост про путешествия');
        $response->assertDontSeeText('Пост без тега');
    }

    public function testSearchByTextReturnsMatchingPosts(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $viewer = User::factory()->create();
        $author = User::factory()->create();
        Post::factory()->create([
            'user_id' => $author->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Байкал и поход',
            'text_content' => 'Поездка на Байкал',
        ]);
        Post::factory()->create([
            'user_id' => $author->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Другое',
            'text_content' => 'Без совпадения',
        ]);

        $response = $this
            ->actingAs($viewer)
            ->get(route('search-results', ['query' => 'Байкал']));

        $response->assertOk();
        $response->assertSeeText('Байкал и поход');
        $response->assertDontSeeText('Другое');
    }

    public function testPopularApiCanSortPostsByLikes(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $viewer = User::factory()->create();
        $firstPost = Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Самый популярный',
        ]);
        $secondPost = Post::factory()->create([
            'user_id' => User::factory()->create()->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Менее популярный',
        ]);

        Like::factory()->count(2)->create(['post_id' => $firstPost->id]);
        Like::factory()->create(['post_id' => $secondPost->id]);

        Sanctum::actingAs($viewer);

        $response = $this->getJson(route('api.popular.index', ['sort' => 'likes']));

        $response->assertOk();
        $response->assertJsonPath('data.0.id', $firstPost->id);
    }
}
