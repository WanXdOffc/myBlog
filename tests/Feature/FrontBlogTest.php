<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);

        Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'First Modern Tech Post',
            'slug' => 'first-modern-tech-post',
            'summary' => 'A summary of the first tech post.',
            'content' => '## Introduction\n\nThis is a sample post.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('First Modern Tech Post');
        $response->assertSee('Laravel');
        $response->assertSee('category-scroll', false);
        $response->assertDontSee('aspect-video');
        $response->assertSee('Lewati ke konten utama');
        $response->assertSee('<main id="main-content"', false);
        $response->assertSee('aria-label="Filter kategori artikel"', false);
    }

    public function test_single_post_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'PHP', 'slug' => 'php']);

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Deep Dive into PHP 8.3',
            'slug' => 'deep-dive-into-php-8-3',
            'summary' => 'Exploring new features of PHP 8.3.',
            'content' => "## Features\n\nPHP 8.3 introduces typed class constants.\n\n```php\nconst BAR = 'foo';\n```",
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/posts/'.$post->slug);

        $response->assertStatus(200);
        $response->assertSee('Deep Dive into PHP 8.3');
        $response->assertSee('Dalam artikel ini');
        $response->assertSee('Bagikan Artikel Ini:');
        $response->assertSee('class="article-prose', false);
        $response->assertSee('id="reading-progress"', false);
        $response->assertSee('id="toc-nav"', false);
    }

    public function test_authenticated_user_can_submit_comment(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'DevOps', 'slug' => 'devops']);

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Docker Best Practices',
            'slug' => 'docker-best-practices',
            'summary' => 'Summary of docker tips.',
            'content' => 'Content about Docker.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $commenter = User::factory()->create();

        $response = $this->actingAs($commenter)->post('/posts/'.$post->slug.'/comments', [
            'body' => 'Artikel ini sangat bermanfaat!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'body' => 'Artikel ini sangat bermanfaat!',
        ]);
    }
}
