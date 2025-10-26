Hello {{ $notifiable->name }},

{{ $sharedBy->name }} has shared a book with you on {{ config('app.name') }}.

---
BOOK DETAILS
---

Title: {{ $userBook->title }}

Shared via: {{ $share->getShareTargetName() }}

@if($userBook->pages)
Pages: {{ $userBook->pages }}
@endif

@if($userBook->edition)
Edition: {{ $userBook->edition }}
@endif

@if($userBook->description)

Description:
{{ $userBook->description }}
@endif

@if($share->notes)

---
MESSAGE FROM {{ strtoupper($sharedBy->name) }}
---

{{ $share->notes }}
@endif

@if($share->expires_at)

⏰ IMPORTANT: This access will expire on {{ $share->expires_at->format('F j, Y') }}
@endif

---

To view this book, visit:
{{ route('user-books.show', $userBook) }}

To see all your shared books, visit:
{{ route('user-books.shared') }}

---

Thank you for using {{ config('app.name') }}!

© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
