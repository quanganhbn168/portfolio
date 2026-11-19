<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function index(): View
    {
        $page = $this->systemPages->require('blog');

        return view('frontend.posts.index', [
            'page' => $page,
            'posts' => Post::query()->published()->with(['curatorMedia', 'slugs'])
                ->orderByDesc('published_at')->orderByDesc('id')->paginate(9),
            'seo' => $this->seo->systemPage($page, 'posts.index'),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->status === 'published' && (! $post->published_at || $post->published_at->isPast()), 404);

        $post->load(['curatorMedia', 'slugs']);

        return view('frontend.posts.show', [
            'post' => $post,
            'bodyHtml' => (string) str((string) $post->body)->sanitizeHtml(),
            'relatedPosts' => Post::query()->published()->whereKeyNot($post->id)->with(['curatorMedia', 'slugs'])
                ->orderByDesc('published_at')->limit(3)->get(),
            'seo' => $this->seo->post($post),
        ]);
    }
}
