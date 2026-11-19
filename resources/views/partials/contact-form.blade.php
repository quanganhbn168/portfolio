{{-- Form liên hệ đơn giản, gửi bằng AJAX (không tải lại trang). $prefix để id không trùng giữa nhiều form. --}}
<form class="quick-form" method="POST" action="{{ route('contact.store') }}" data-ajax-contact novalidate>
    @csrf
    <x-honeypot />
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="{{ $prefix }}-name">{{ __('Họ và tên') }} <span class="text-danger">*</span></label>
            <input class="form-control" id="{{ $prefix }}-name" name="name" type="text" autocomplete="name" maxlength="255" required>
            <div class="invalid-feedback" data-error-for="name"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="{{ $prefix }}-phone">{{ __('Số điện thoại') }} <span class="text-danger">*</span></label>
            <input class="form-control" id="{{ $prefix }}-phone" name="phone" type="tel" autocomplete="tel" maxlength="32" required>
            <div class="invalid-feedback" data-error-for="phone"></div>
        </div>
        <div class="col-12">
            <label class="form-label" for="{{ $prefix }}-message">{{ __('Nội dung') }} <span class="text-danger">*</span></label>
            <textarea class="form-control" id="{{ $prefix }}-message" name="message" rows="{{ $rows ?? 3 }}" maxlength="5000" required></textarea>
            <div class="invalid-feedback" data-error-for="message"></div>
        </div>
    </div>
    <div class="quick-form__status" data-contact-status role="status" aria-live="polite"></div>
    <button class="btn btn-primary btn-lg w-100 mt-3" type="submit">{{ __('Gửi yêu cầu') }}</button>
</form>
