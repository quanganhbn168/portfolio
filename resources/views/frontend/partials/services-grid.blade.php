{{-- Lưới dịch vụ (trang chủ và trang Dịch vụ & bảng giá). Cần biến $services. --}}
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
    @foreach ($services as $service)
        <div class="col">
            <article class="service-card">
                <span class="service-card__icon"><i class="fa-solid {{ $service->icon }}" aria-hidden="true"></i></span>
                <h3>{{ $service->title }}</h3>
                @if ($service->description)<p>{{ $service->description }}</p>@endif
            </article>
        </div>
    @endforeach
</div>
