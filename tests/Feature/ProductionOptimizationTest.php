<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rss_feed_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);
        Post::create([
            'user_id'      => $user->id,
            'category_id'  => $category->id,
            'title'        => 'RSS Feed Post Title',
            'slug'         => 'rss-feed-post-title',
            'summary'      => 'Summary text',
            'content'      => 'Content text',
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/feed');

        $response->assertStatus(200);
        $response->assertSee('RSS Feed Post Title');
    }

    public function test_custom_404_page_rendered_for_invalid_route(): void
    {
        $response = $this->get('/non-existent-page-url-xyz');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
    }

    public function test_comment_submission_is_rate_limited(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Security', 'slug' => 'security']);
        $post = Post::create([
            'user_id'      => $user->id,
            'category_id'  => $category->id,
            'title'        => 'Rate Limited Post',
            'slug'         => 'rate-limited-post',
            'summary'      => 'Summary',
            'content'      => 'Content',
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $commenter = User::factory()->create();

        // 1st comment -> success
        $this->actingAs($commenter)->post('/posts/' . $post->slug . '/comments', ['body' => 'First comment']);
        // 2nd comment -> success
        $this->actingAs($commenter)->post('/posts/' . $post->slug . '/comments', ['body' => 'Second comment']);

        // 3rd comment -> Rate limit 429 Too Many Requests
        $response = $this->actingAs($commenter)->post('/posts/' . $post->slug . '/comments', ['body' => 'Third comment']);
        $response->assertStatus(429);
    }
}
