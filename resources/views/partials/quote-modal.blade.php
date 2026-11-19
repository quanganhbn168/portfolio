{{-- Modal "Nhận báo giá": form đơn giản ở trên, các cách liên lạc ở dưới. Mở bằng data-bs-toggle="modal" data-bs-target="#quote-modal" (data-plan để điền sẵn tên gói). --}}
<div class="modal fade" id="quote-modal" tabindex="-1" aria-labelledby="quote-modal-title" aria-hidden="true" data-plan-text="{{ __('Mình muốn nhận báo giá gói “:p1”.', ['p1' => ':plan']) }}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-4 fw-bold" id="quote-modal-title">{{ __('Nhận báo giá & tư vấn') }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Đóng') }}"></button>
            </div>
            <div class="modal-body">
                <p class="text-body-secondary">{{ __('Để lại thông tin, mình sẽ liên hệ lại sớm nhất.') }}</p>
                @include('partials.contact-form', ['prefix' => 'quote'])

                <div class="quote-modal__methods">
                    <p class="quote-modal__or"><span>{{ __('Hoặc liên hệ trực tiếp qua') }}</span></p>
                    @include('partials.contact-methods', ['compact' => true])
                </div>
            </div>
        </div>
    </div>
</div>
