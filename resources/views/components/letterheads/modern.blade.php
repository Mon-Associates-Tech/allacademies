@props(['school', 'title' => 'Document'])

<div style="padding: 15px 0; border-bottom: 1px solid #e5e7eb; margin-bottom: 30px;">
    <div style="text-align: center;">
        @if($school->logo)
            <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                 style="max-width: 80px; max-height: 80px; margin-bottom: 15px;">
        @endif
        <h1 style="font-size: 24px; font-weight: 600; color: #111827; margin: 0 0 8px;">{{ $school->name }}</h1>
        <p style="font-size: 13px; color: #6b7280; margin: 0;">
            {{ $school->address }}, {{ $school->city }} • {{ $school->phone }} • {{ $school->email }}
        </p>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <h2 style="font-size: 18px; font-weight: 500; color: #374151; margin: 0; text-transform: uppercase; letter-spacing: 2px;">
            {{ $title }}
        </h2>
    </div>
</div>
