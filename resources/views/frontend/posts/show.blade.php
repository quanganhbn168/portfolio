@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/blog.css')
@endpush

@section('content')
    <article class="blog-post">
        <header class="blog-post__head container">
            <nav class="blog-post__crumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ __('Trang chủ') }}</a><span>/</span><a href="{{ route('posts.index') }}">{{ __('Blog') }}</a>
            </nav>
            <h1>{{ $post->title }}</h1>
            <p class="blog-post__meta">
                <time datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">{{ ($post->published_at ?? $post->created_at)->translatedFormat('d/m/Y') }}</time>
                <span aria-hidden="true">·</span>
                <span>{{ __(':p1 phút đọc', ['p1' => $post->reading_minutes]) }}</span>
                @if (filled($website->owner_name))<span aria-hidden="true">·</span><span>{{ $website->owner_name }}</span>@endif
            </p>
            @if ($post->excerpt)<p class="blog-post__lead">{{ $post->excerpt }}</p>@endif
        </header>

        @if ($post->curatorMedia)
            <div class="container blog-post__cover">
                <x-responsive-image :media="$post->curatorMedia" :alt="$post->title" :widths="[768, 1200, 1600]" sizes="(min-width: 1200px) 1100px, 100vw" :priority="true" />
            </div>
        @endif

        <div class="container blog-post__content">
            <div class="article-prose">{!! $bodyHtml !!}</div>
        </div>
    </article>

    @if ($relatedPosts->isNotEmpty())
        <section class="section-space pt-0">
            <div class="container">
                <h2 class="blog-related__title">{{ __('Bài viết khác') }}</h2>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach ($relatedPosts as $post)
                        <div class="col">@include('frontend.partials.post-card')</div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
