@php($languages = config('languages.supported'))
<div class="dropdown language-switcher">
    <button class="language-switcher__toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Ngôn ngữ') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>
        <span>{{ strtoupper(app()->getLocale()) }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end notranslate" translate="no">
        @foreach ($languages as $code => $label)
            <li>
                <form method="POST" action="{{ route('language.switch', ['locale' => $code]) }}">
                    @csrf
                    <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}">
                    <button class="dropdown-item @if (app()->getLocale() === $code) active @endif" type="submit" lang="{{ $code }}">{{ $label }}</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
