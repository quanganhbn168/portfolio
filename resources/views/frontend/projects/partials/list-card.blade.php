{{-- Thẻ dự án dạng ảnh trên, thông tin dưới (trang danh sách dự án). Thẻ phủ chữ lên ảnh dùng frontend.partials.project-card. --}}
@php
    $url = route('slug.show', ['slug' => $project->slug]);
    $facts = collect([
        ['label' => __('Khách hàng'), 'icon' => 'fa-user-tie', 'value' => $project->client_name],
        ['label' => __('Giá trị'), 'icon' => 'fa-coins', 'value' => $project->price],
        ['label' => __('Hoàn thành'), 'icon' => 'fa-calendar-check', 'value' => $project->completed_at?->translatedFormat('m/Y')],
    ])->filter(fn (array $fact): bool => filled($fact['value']))->take(3);
@endphp
<article class="project-tile">
    <div class="project-tile__media">
        <a class="project-tile__image" href="{{ $url }}" tabindex="-1" aria-hidden="true">
        @if ($project->curatorMedia)
            <x-responsive-image :media="$project->curatorMedia" :alt="$project->title" :widths="[480, 768]"
                sizes="(min-width: 1200px) 380px, (min-width: 768px) 46vw, 100vw" />
        @elseif ($project->image_url)
            <img src="{{ $project->image_card_url }}" alt="{{ $project->title }}" loading="lazy">
        @else
            <img src="{{ asset('images/no-image.svg') }}" alt="" loading="lazy">
        @endif
        </a>
        @if ($project->category)
            <span class="project-tile__badge">{{ $project->category->name }}</span>
        @endif
        <span class="status-pill status-pill--{{ $project->project_status }} project-tile__status">{{ $project->project_status_label }}</span>
        <div class="project-tile__overlay">
            @if ($project->website_url)
                <a class="btn btn-primary" href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square me-1" aria-hidden="true"></i>{{ __('Xem thực tế') }}</a>
            @endif
            <a class="btn btn-light" href="{{ $url }}">{{ __('Xem chi tiết') }}</a>
        </div>
    </div>
    <div class="project-tile__body">
        <h3 class="project-tile__title"><a href="{{ $url }}">{{ $project->title }}</a></h3>
        @if ($project->excerpt)
            <p class="project-tile__excerpt">{{ $project->excerpt }}</p>
        @endif
        @if ($facts->isNotEmpty())
            <ul class="project-tile__facts">
                @foreach ($facts as $fact)
                    <li title="{{ $fact['label'] }}"><i class="fa-solid {{ $fact['icon'] }}" aria-hidden="true"></i><span class="visually-hidden">{{ $fact['label'] }}: </span>{{ $fact['value'] }}</li>
                @endforeach
            </ul>
        @endif
        @if (count($project->technology_list))
            <ul class="tech-chips tech-chips--sm" aria-label="{{ __('Công nghệ sử dụng') }}">
                @foreach (array_slice($project->technology_list, 0, 4) as $tech)<li>{{ $tech }}</li>@endforeach
            </ul>
        @endif
        <div class="project-tile__actions project-tile__actions--touch">
            <a class="project-tile__more" href="{{ $url }}">{{ __('Xem chi tiết') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            @if ($project->website_url)
                <a class="project-tile__live" href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer">{{ __('Xem website') }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            @endif
        </div>
    </div>
</article>
