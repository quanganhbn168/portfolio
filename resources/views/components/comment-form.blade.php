@props(['commentable', 'compact' => false, 'ratingEnabled' => false])

<form class="{{ $compact ? '' : 'mt-5 ' }} comment-form" action="{{ route('comments.store', ['type' => $commentable->getMorphClass(), 'id' => $commentable->getKey()]) }}" method="post">
    @csrf
    <x-honeypot />
    <div class="row g-3">
        <label class="fw-semibold col-md-6" for="comment-author-name">
            {{ __('Họ tên') }} <span class="text-danger">*</span>
            <input class="form-control" id="comment-author-name" name="author_name" type="text" value="{{ old('author_name') }}" autocomplete="name" required maxlength="120">
            @error('author_name')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
        </label>
        <label class="fw-semibold col-md-6" for="comment-author-email">
            Email <span class="fw-normal text-body-secondary small">{{ __('(không bắt buộc)') }}</span>
            <input class="form-control" id="comment-author-email" name="author_email" type="email" value="{{ old('author_email') }}" autocomplete="email" maxlength="255">
            @error('author_email')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
        </label>
        @if ($ratingEnabled)
            <fieldset class="fw-semibold col-12">
                <legend class="fs-6 fw-semibold mb-1">{{ __('Đánh giá') }} <span class="text-danger">*</span></legend>
                {{-- Các nút nằm theo thứ tự 5→1 và được đảo chiều bằng CSS để tô sao từ trái sang phải chỉ với CSS. --}}
                <div class="star-rating">
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <input class="star-rating__input" type="radio" name="rating" id="comment-rating-{{ $rating }}"
                            value="{{ $rating }}" @checked((int) old('rating') === $rating) required>
                        <label class="star-rating__star" for="comment-rating-{{ $rating }}"
                            title="{{ $rating }} sao"><span class="visually-hidden">{{ $rating }} sao</span></label>
                    @endfor
                </div>
                @error('rating')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
            </fieldset>
        @endif
        <label class="fw-semibold col-12" for="comment-body">
            {{ __('Bình luận') }} <span class="text-danger">*</span>
            <textarea class="form-control comment-form__message" id="comment-body" name="body" required maxlength="3000">{{ old('body') }}</textarea>
            @error('body')<span class="d-block fw-medium text-danger small mt-1">{{ $message }}</span>@enderror
        </label>
    </div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
        <p class="small text-body-secondary mb-0">{{ __('Bình luận sẽ được kiểm duyệt trước khi hiển thị công khai.') }}</p>
        <button class="btn btn-primary" type="submit">{{ __('Gửi bình luận') }}</button>
    </div>
</form>
