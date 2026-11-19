{{-- Đánh giá của khách hàng + form gửi bình luận, dùng chung cho năng lực và dự án. --}}

<section class="resource-related-section" id="phan-hoi">
    <div class="container">
        <header class="mx-auto text-center mb-4">
            <h2 class="display-title h2">{{ $heading }}</h2>
            @if ($ratingSummary['count'])
                <p class="text-body"><strong class="text-primary">{{ number_format($ratingSummary['average'], 1) }}/5</strong>
                    {{ __('từ :p1 đánh giá đã được duyệt.', ['p1' => $ratingSummary['count']]) }}</p>
            @else
                <p class="text-body">{{ __('Những đánh giá đầu tiên sẽ được hiển thị sau khi đội ngũ kiểm duyệt.') }}</p>
            @endif
        </header>

        <div class="review-layout">
            <div class="d-grid gap-4">
                @forelse ($commentable->approvedComments as $comment)
                    <article class="d-flex gap-3">
                        <span class="d-grid flex-shrink-0 fw-bold review-avatar"
                            aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}</span>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-baseline gap-2">
                                <h3 class="fw-semibold h6 mb-0">{{ $comment->author_name }}</h3>
                                <time class="fw-medium small text-body-secondary"
                                    datetime="{{ $comment->approved_at?->toDateString() }}">{{ $comment->approved_at?->translatedFormat('d/m/Y') }}</time>
                            </div>
                            @if ($comment->rating)
                                <p class="review-rating mt-2 mb-0" aria-label="{{ __(':p1 trên 5 sao', ['p1' => $comment->rating]) }}">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <span class="{{ $star <= $comment->rating ? '' : 'text-body-tertiary' }}"
                                            aria-hidden="true">★</span>
                                    @endfor
                                </p>
                            @endif
                            <p class="mt-2 mb-0">{{ $comment->body }}</p>
                        </div>
                    </article>
                @empty
                    <p class="empty-state w-100">{{ __('Chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ trải nghiệm.') }}</p>
                @endforelse
            </div>

            <x-comment-form :commentable="$commentable" :rating-enabled="true" :compact="true" />
        </div>
    </div>
</section>
