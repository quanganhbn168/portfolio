@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/blog.css')
@endpush

@section('content')
    <section class="blog-head">
        <div class="container">
            <span class="pf-badge">{{ __('Blog') }}</span>
            <h1>{{ $page['title'] }}</h1>
            @if (filled($page['excerpt']))<p>{{ $page['excerpt'] }}</p>@endif
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @forelse ($posts as $post)
                    <div class="col">@include('frontend.partials.post-card')</div>
                @empty
                    <p class="empty-state w-100">{{ __('Chưa có bài viết nào.') }}</p>
                @endforelse
            </div>

            @if ($posts->hasPages())
                <div class="mt-4">{{ $posts->onEachSide(1)->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>
@endsection
