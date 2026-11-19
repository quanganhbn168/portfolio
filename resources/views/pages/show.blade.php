@extends('layouts.master')

@section('content')
<article class="container py-5" style="max-width: 56rem">
    <nav aria-label="{{ __('Đường dẫn') }}" class="mb-4 small"><a href="{{ route('home') }}">{{ __('Trang chủ') }}</a> / <span aria-current="page">{{ $page->title }}</span></nav>
    <h1 class="fw-bold display-title">{{ $page->title }}</h1>
    @if($page->excerpt)<p class="lead">{{ $page->excerpt }}</p>@endif
    @if($page->image_url)<img src="{{ $page->image_webp_url }}" alt="{{ $page->title }}" class="w-100 img-fluid rounded my-4">@endif
    <div class="article-prose mt-4">{!! $content !!}</div>
</article>
@endsection
