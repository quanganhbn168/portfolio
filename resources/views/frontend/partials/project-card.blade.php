@php
    $url = route('slug.show', ['slug' => $project->slug]);
    $imageUrl = $project->image_card_url ?: ($defaultBannerUrl ?? null);
    $facts = collect([
        ['label' => __('Chủ đầu tư'), 'icon' => 'fa-user-tie', 'value' => $project->client_name],
        ['label' => __('Địa điểm'), 'icon' => 'fa-location-dot', 'value' => data_get($project->details, 'hero.location')],
        ['label' => __('Quy mô'), 'icon' => 'fa-ruler-combined', 'value' => data_get($project->details, 'hero.area')],
        ['label' => __('Kinh phí'), 'icon' => 'fa-coins', 'value' => data_get($project->details, 'hero.budget')],
        ['label' => __('Loại công trình'), 'icon' => 'fa-industry', 'value' => $project->industry],
        ['label' => __('Hoàn thành'), 'icon' => 'fa-calendar-check', 'value' => $project->completed_at?->translatedFormat('m/Y')],
    ])->filter(fn (array $fact): bool => filled($fact['value']))->take(4);
@endphp
<article class="project-card">
    @if ($project->curatorMedia)
        {{-- Thẻ dọc 3:4 cắt ảnh ngang bằng object-fit: cover, nên chiều rộng cần tải bằng khoảng 2,4 lần bề rộng thẻ. --}}
        <x-responsive-image class="project-card__image" :media="$project->curatorMedia" :alt="$project->title"
            :widths="[480, 768, 1200]"
            sizes="(min-width: 1200px) 800px, (min-width: 768px) calc(46vw * 2.4), calc(100vw * 2.4)" />
    @elseif ($imageUrl)
        <img class="project-card__image" src="{{ $imageUrl }}" alt="{{ $project->title }}" loading="lazy">
    @else
        <img class="project-card__image" src="{{ asset('images/no-image.svg') }}" alt="" loading="lazy">
    @endif
    @if ($showVideoCue ?? false)
        <span class="project-card__video-cue" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
    @endif
    <div class="project-card__body">
        @if ($project->category)
            <p class="project-card__category">{{ $project->category->name }}</p>
        @endif
        <h3 class="project-card__title"><a class="stretched-link" href="{{ $url }}">{{ $project->title }}</a></h3>
        @if ($project->excerpt || $facts->isNotEmpty())
            <div class="project-card__more">
                <div>
                    @if ($project->excerpt)
                        <p class="project-card__excerpt">{{ $project->excerpt }}</p>
                    @endif
                    @if ($facts->isNotEmpty())
                        <dl class="project-card__facts">
                            @foreach ($facts as $fact)
                                <div title="{{ $fact['label'] }}"><dt><i class="fa-solid {{ $fact['icon'] }}" aria-hidden="true"></i><span class="visually-hidden">{{ $fact['label'] }}</span></dt><dd>{{ $fact['value'] }}</dd></div>
                            @endforeach
                        </dl>
                    @endif
                </div>
            </div>
        @endif
    </div>
</article>
