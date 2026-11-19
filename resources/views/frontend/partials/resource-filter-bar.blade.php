<div class="resource-filter-bar">
    <div class="resource-filter-bar__categories" aria-label="{{ __('Lọc theo danh mục') }}">
        <a class="resource-filter  {{ ! $activeCategory ? 'is-active' : '' }}" href="{{ route($resourceIndexRoute) }}">{{ __('Tất cả :p1', ['p1' => mb_strtolower($resourceName)]) }}</a>
        @foreach ($categories as $category)
            <a class="resource-filter  {{ $activeCategory?->is($category) ? 'is-active' : '' }}" href="{{ $category->public_url ?? route('slug.show', ['slug' => $category->slug]) }}">
                {{ $category->tree_label ?? $category->name }}
                @if ($category->{$categoryCountAttribute})
                    <span class="resource-filter__count">{{ $category->{$categoryCountAttribute} }}</span>
                @endif
            </a>
        @endforeach
    </div>
    <form class="resource-sort" method="GET" action="{{ url()->current() }}">
        <label class="form-label small mb-0" for="resource-sort">{{ __('Sắp xếp :p1', ['p1' => mb_strtolower($resourceName)]) }}</label>
        <select class="form-select" id="resource-sort" name="sort" onchange="this.form.submit()">
            @foreach ($sortOptions as $value => $label)
                <option value="{{ $value }}" @selected($sort === $value)>{{ __($label) }}</option>
            @endforeach
        </select>
    </form>
</div>
