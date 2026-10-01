<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_like_via_ajax(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $post = Post::create([
            'user_id'      => $user->id,
            'category_id'  => $category->id,
            'title'        => 'Interactive Post',
            'slug'         => 'interactive-post',
            'summary'      => 'Summary',
            'content'      => 'Content',
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson('/posts/' . $post->slug . '/like');

        $response->assertStatus(200);
        $response->assertJson(['liked' => true, 'likes_count' => 1]);
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'post_id' => $post->id]);

        // Toggle like off
        $response2 = $this->actingAs($user)->postJson('/posts/' . $post->slug . '/like');
        $response2->assertJson(['liked' => false, 'likes_count' => 0]);
    }

    public function test_user_can_toggle_bookmark_and_view_bookmarks_page(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $post = Post::create([
            'user_id'      => $user->id,
            'category_id'  => $category->id,
            'title'        => 'Bookmarked Article',
            'slug'         => 'bookmarked-article',
            'summary'      => 'Summary',
            'content'      => 'Content',
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson('/posts/' . $post->slug . '/bookmark');
        $response->assertStatus(200);
        $response->assertJson(['bookmarked' => true]);

        $bookmarksPage = $this->actingAs($user)->get('/bookmarks');
        $bookmarksPage->assertStatus(200);
        $bookmarksPage->assertSee('Bookmarked Article');
    }

    public function test_scout_live_search_returns_json_results(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'React', 'slug' => 'react']);
        Post::create([
            'user_id'      => $user->id,
            'category_id'  => $category->id,
            'title'        => 'Mastering React 19 Hooks',
            'slug'         => 'mastering-react-19-hooks',
            'summary'      => 'React 19 summary',
            'content'      => 'React hooks overview',
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/search?q=React');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Mastering React 19 Hooks']);
    }
}
