{{-- Các gói giá. Cần biến $plans. --}}
<div class="row g-4 justify-content-center pricing-grid">
    @foreach ($plans as $plan)
        <div class="col-12 col-md-6 col-xl-{{ max(3, intdiv(12, max(1, min($plans->count(), 4)))) }}">
            <article class="price-card @if ($plan->is_featured) price-card--featured @endif">
                @if ($plan->is_featured)<span class="price-card__flag">{{ __('Phổ biến') }}</span>@endif
                <h3>{{ $plan->name }}</h3>
                @if ($plan->description)<p class="price-card__desc">{{ $plan->description }}</p>@endif
                <p class="price-card__price"><strong>{{ $plan->price }}</strong>@if ($plan->price_note) <small>{{ $plan->price_note }}</small>@endif</p>
                @if (count($plan->feature_list))
                    <ul class="price-card__features">
                        @foreach ($plan->feature_list as $feature)
                            <li><i class="fa-solid fa-check" aria-hidden="true"></i>{{ $feature }}</li>
                        @endforeach
                    </ul>
                @endif
                <button type="button" class="btn @if ($plan->is_featured) btn-primary @else btn-outline-secondary @endif btn-lg w-100" data-bs-toggle="modal" data-bs-target="#quote-modal" data-plan="{{ $plan->name }}">{{ $plan->button_label ?: __('Nhận báo giá') }}</button>
            </article>
        </div>
    @endforeach
</div>
