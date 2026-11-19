<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\NewContactRequest;
use App\Models\ContactRequest;
use App\Rules\PhoneNumber;
use App\Settings\WebsiteSettings;
use App\Support\Maps\GoogleMapsUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly WebsiteSettings $website,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function index(Request $request): View
    {
        $page = $this->systemPages->require('contact');

        return view('frontend.contact', [
            'prefilledMessage' => '',
            'contactPhones' => $this->contactPhones(),
            'contactBranches' => $this->contactBranches(),
            'page' => $page,
            'pageBannerUrl' => $page['banner_url'],
            'seo' => $this->seo->systemPage($page, 'contact'),
        ]);
    }

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

    /** @return Collection<int, array{label: string, href: string}> */
    private function contactPhones(): Collection
    {
        $phones = collect($this->website->phones ?? [])
            ->filter(fn ($phone): bool => is_array($phone) && filled($phone['number'] ?? null))
            ->values();

        if ($phones->isEmpty()) {
            $phones = collect([
                ['number' => $this->website->hotline],
                ['number' => $this->website->contact_phone],
            ])->filter(fn (array $phone): bool => filled($phone['number'] ?? null))->values();
        }

        return $phones->map(fn (array $phone): array => [
            'label' => trim((string) $phone['number']),
            'href' => 'tel:'.preg_replace('/\s+/', '', (string) $phone['number']),
        ])->unique('href')->values();
    }

    /**
     * Các địa điểm hiển thị ở trang Liên hệ, mỗi nơi kèm link bản đồ nhúng riêng (tự tạo từ địa chỉ nếu chưa nhập).
     *
     * @return Collection<int, array{name: string, address: string, phone: string, map: string}>
     */
    private function contactBranches(): Collection
    {
        $branches = collect($this->website->branches ?? [])
            ->filter(fn ($branch): bool => is_array($branch)
                && ($branch['is_active'] ?? true)
                && filled($branch['address'] ?? null))
            ->map(fn (array $branch): array => [
                'name' => (string) ($branch['name'] ?? __('Địa chỉ')),
                'address' => trim((string) $branch['address']),
                'phone' => trim((string) ($branch['phone'] ?? '')),
                'map' => GoogleMapsUrl::normalizeEmbed($branch['map_url'] ?? null) ?: $this->addressMapUrl(trim((string) $branch['address'])),
            ])
            ->values();

        if ($branches->isEmpty() && filled($this->website->address)) {
            // Chưa khai báo chi nhánh: dùng địa chỉ chung (và bản đồ nhúng trong Cài đặt chung nếu có).
            $branches = collect([[
                'name' => __('Địa chỉ'),
                'address' => trim((string) $this->website->address),
                'phone' => '',
                'map' => GoogleMapsUrl::normalizeEmbed($this->website->google_maps_embed_url) ?: $this->addressMapUrl(trim((string) $this->website->address)),
            ]]);
        }

        return $branches;
    }

    private function addressMapUrl(string $address): string
    {
        return 'https://www.google.com/maps?q='.rawurlencode($address).'&output=embed';
    }

    /** A failing mail server must never lose the lead or break the form: it is already stored. */
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
