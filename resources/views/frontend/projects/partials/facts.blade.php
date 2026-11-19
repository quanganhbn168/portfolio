{{-- Thông số dự án: khách hàng, giá trị, thời gian, tình trạng, công nghệ và nút xem website thực tế. --}}
@if (count($content['overview']['facts']))<dl class="project-case__facts">
    @foreach ($content['overview']['facts'] as $fact)
        <div><dt>{{ $fact['label'] }}</dt><dd>@if (isset($fact['status']))<span class="status-pill status-pill--{{ $fact['status'] }}">{{ $fact['value'] }}</span>@else{{ $fact['value'] }}@endif</dd></div>
    @endforeach
</dl>@endif
@if (count($content['overview']['technologies']))<ul class="tech-chips" aria-label="{{ __('Công nghệ sử dụng') }}">@foreach ($content['overview']['technologies'] as $tech)<li>{{ $tech }}</li>@endforeach</ul>@endif
@if ($project->website_url)<a class="btn btn-primary mt-3" href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square me-2" aria-hidden="true"></i>{{ __('Xem website thực tế') }}</a>@endif
