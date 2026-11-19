Có liên hệ mới trên website.

Họ tên: {{ $contactRequest->name }}
Điện thoại: {{ $contactRequest->phone ?: '-' }}
Email: {{ $contactRequest->email ?: '-' }}
Công ty: {{ $contactRequest->company ?: '-' }}
Ngân sách: {{ $contactRequest->budget ?: '-' }}

Nội dung:
{{ $contactRequest->message }}

Xem trong admin: {{ url('/admin/contact-requests/'.$contactRequest->id.'/edit') }}
