@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/projects-index.css')
@endpush

@section('content')
    @if ($activeCategory)
        @include('frontend.partials.resource-archive-hero', [
            'resourceName' => __('Dự án'),
            'resourceIndexRoute' => 'projects.index',
            'pageKey' => $page['key'] ?? null,
            'pageBannerUrl' => $pageBannerUrl ?? null,
        ])

        <section class="section-space" id="du-an-da-trien-khai">
            <div class="container">
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    @forelse ($projects as $project)
                        <div class="col">@include('frontend.projects.partials.list-card')</div>
                    @empty
                        <p class="empty-state w-100">{{ __('Chưa có dự án được xuất bản trong danh mục này.') }}</p>
                    @endforelse
                </div>

                @if ($projects->hasPages())
                    <div class="mt-4">{{ $projects->onEachSide(1)->links('frontend.partials.pagination') }}</div>
                @endif
            </div>
        </section>

        @include('frontend.partials.category-content')
    @else
        @include('frontend.projects.partials.portfolio')
    @endif
@endsection
