<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class FrontPostController extends Controller
{
    /**
     * Display post list (Homepage & Blog archive).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categorySlug = $request->query('category');
        $tagSlug = $request->query('tag');

        if ($search) {
            // Using Laravel Scout for title/content search
            $scoutIds = Post::search($search)->keys();
            $query = Post::published()->whereIn('id', $scoutIds)->with(['category', 'user', 'tags']);
        } else {
            $query = Post::published()->with(['category', 'user', 'tags']);
        }

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($tagSlug) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        $posts = $query->paginate(9)->withQueryString();

        $categories = Category::withCount(['posts' => function ($q) {
            $q->where('status', 'published');
        }])->get();

        $popularTags = Tag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();

        $activeCategory = $categorySlug ? Category::where('slug', $categorySlug)->first() : null;
        $activeTag = $tagSlug ? Tag::where('slug', $tagSlug)->first() : null;

        return view('posts.index', compact(
            'posts',
            'categories',
            'popularTags',
            'activeCategory',
            'activeTag',
            'search'
        ));
    }

    /**
     * Display a single post page.
     */
    public function show(string $slug)
    {
        $post = Post::published()
            ->with(['user', 'category', 'tags', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->take(3)
            ->get();

        $isLiked = auth()->check() ? auth()->user()->hasLiked($post) : false;
        $isBookmarked = auth()->check() ? auth()->user()->hasBookmarked($post) : false;

        return view('posts.show', compact('post', 'relatedPosts', 'isLiked', 'isBookmarked'));
    }

    /**
     * Store comment on a post.
     */
    public function storeComment(Request $request, string $slug)
    {
        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->input('body'),
        ]);

        return back()->with('success', 'Komentar Anda berhasil dipublikasikan!');
    }

    /**
     * Toggle Like / Clap on a post (AJAX).
     */
    public function toggleLike(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        $existing = Like::where('user_id', $user->id)->where('post_id', $post->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $liked = true;
        }

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    /**
     * Toggle Bookmark on a post (AJAX).
     */
    public function toggleBookmark(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        $existing = Bookmark::where('user_id', $user->id)->where('post_id', $post->id)->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $bookmarked = true;
        }

        return response()->json([
            'bookmarked' => $bookmarked,
        ]);
    }

    /**
     * Display user bookmarked posts page.
     */
    public function bookmarks()
    {
        $user = auth()->user();

        $postIds = Bookmark::where('user_id', $user->id)->pluck('post_id');

        $posts = Post::published()
            ->whereIn('id', $postIds)
            ->with(['category', 'user'])
            ->paginate(9);

        return view('posts.bookmarks', compact('posts'));
    }

    /**
     * Live Search API endpoint using Laravel Scout.
     */
    public function liveSearch(Request $request)
    {
        $query = trim($request->query('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $posts = Post::search($query)
            ->take(5)
            ->get()
            ->filter(fn ($p) => $p->status === 'published')
            ->map(function ($post) {
                return [
                    'id'            => $post->id,
                    'title'         => $post->title,
                    'slug'          => $post->slug,
                    'category_name' => $post->category?->name ?? 'Article',
                    'url'           => route('posts.show', $post->slug),
                    'date'          => $post->published_at?->format('M d, Y'),
                ];
            })
            ->values();

        return response()->json(['results' => $posts]);
    }
}
