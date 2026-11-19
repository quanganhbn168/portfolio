<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\NewContactRequest;
use App\Models\ContactRequest;
use App\Rules\PhoneNumber;
use App\Settings\WebsiteSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function __construct(private readonly WebsiteSettings $website) {}

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        // Lưu số điện thoại dạng chuẩn (chỉ chữ số, đầu 0) để dễ gọi lại và tìm trùng.
        if (is_string($request->input('phone'))) {
            $request->merge(['phone' => PhoneNumber::normalize($request->input('phone'))]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:32', new PhoneNumber],
            'company' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ], [], [
            'name' => __('họ và tên'),
            'phone' => __('số điện thoại'),
            'email' => 'email',
            'company' => __('tên công ty'),
            'message' => __('nội dung'),
        ]);

        // Honeypot: bots fill the hidden field; pretend success without storing anything.
        if (! $request->filled('website_url')) {
            $this->notify(ContactRequest::query()->create($data));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.'),
            ]);
        }

        return redirect()->to($this->safeReturnPath($request->input('return_to')) ?? route('contact'))
            ->with('success', __('Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.'));
    }

    private function notify(ContactRequest $contactRequest): void
    {
        $recipient = trim((string) $this->website->contact_email);

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new NewContactRequest($contactRequest));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function safeReturnPath(mixed $returnTo): ?string
    {
        if (! is_string($returnTo)) {
            return null;
        }

        $returnTo = trim($returnTo);

        return str_starts_with($returnTo, '/') && ! str_starts_with($returnTo, '//')
            ? $returnTo
            : null;
    }
}
