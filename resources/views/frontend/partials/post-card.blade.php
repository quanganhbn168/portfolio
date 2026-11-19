@php
    $url = route('slug.show', ['slug' => $post->slug]);
@endphp
<article class="post-card">
    <a class="post-card__media" href="{{ $url }}" tabindex="-1" aria-hidden="true">
        @if ($post->curatorMedia)
            <x-responsive-image :media="$post->curatorMedia" :alt="$post->title" :widths="[480, 768]" sizes="(min-width: 1200px) 380px, (min-width: 768px) 46vw, 100vw" />
        @else
            <img src="{{ asset('images/no-image.svg') }}" alt="" loading="lazy">
        @endif
    </a>
    <div class="post-card__body">
        <p class="post-card__meta">
            <time datetime="{{ ($post->published_at ?? $post->created_at)->toDateString() }}">{{ ($post->published_at ?? $post->created_at)->translatedFormat('d/m/Y') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ __(':p1 phút đọc', ['p1' => $post->reading_minutes]) }}</span>
        </p>
        <h3 class="post-card__title"><a href="{{ $url }}">{{ $post->title }}</a></h3>
        @if ($post->excerpt)<p class="post-card__excerpt">{{ $post->excerpt }}</p>@endif
        <a class="post-card__more" href="{{ $url }}">{{ __('Đọc tiếp') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</article>
