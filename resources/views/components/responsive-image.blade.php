{{--
    Ảnh responsive từ thư viện media: nhiều kích thước (srcset), định dạng WebP, lazy load.
    Ảnh gốc luôn được giữ nguyên; các bản nhỏ do Curator/Glide tạo và cache khi có người xem.
    SVG hoặc ảnh không thể resize được in nguyên bản.
    Truyền :convert="false" ở chỗ nào cần giữ ảnh nguyên bản (logo, ảnh có chữ nhỏ...).
--}}
@props([
    'media' => null,
    'alt' => '',
    'sizes' => '100vw',
    'widths' => [480, 768, 1200],
    'priority' => false,
    'quality' => 90,
    'convert' => true,
    'placeholder' => false,
])

@if ($media instanceof \Awcodes\Curator\Models\Media)
    @php($sources = $convert ? \App\Support\Media\MediaUrl::responsive($media, $widths, quality: (int) $quality) : null)
    @if ($sources)
        <img
            {{ $attributes }}
            src="{{ $sources['src'] }}"
            srcset="{{ $sources['srcset'] }}"
            sizes="{{ $sizes }}"
            alt="{{ $alt }}"
            @if ($media->width && $media->height) width="{{ $media->width }}" height="{{ $media->height }}" @endif
            @if ($priority) fetchpriority="high" @else loading="lazy" decoding="async" @endif>
    @else
        <img {{ $attributes }} src="{{ \App\Support\Media\MediaUrl::versioned($media) }}" alt="{{ $alt }}"
            @if ($media->width && $media->height) width="{{ $media->width }}" height="{{ $media->height }}" @endif
            @if ($priority) fetchpriority="high" @else loading="lazy" decoding="async" @endif>
    @endif
@elseif ($placeholder)
    <img {{ $attributes }} src="{{ asset('images/no-image.svg') }}" alt="" loading="lazy">
@endif
