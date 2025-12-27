@props(['school', 'title' => 'Document'])

<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; margin-bottom: 30px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        @if($school->logo)
            <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                 style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid white; background: white; padding: 5px;">
        @endif
        <div style="flex: 1;">
            <h1 style="font-size: 32px; font-weight: bold; margin: 0;">{{ $school->name }}</h1>
            <p style="font-size: 16px; margin: 10px 0 5px;">{{ $school->address }}, {{ $school->city }}, {{ $school->state }}</p>
            <p style="font-size: 14px; margin: 0;">
                {{ $school->phone }} • {{ $school->email }}
                @if($school->website) • {{ $school->website }}@endif
            </p>
        </div>
    </div>
    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(255,255,255,0.3);">
        <h2 style="font-size: 22px; font-weight: 600; margin: 0; text-transform: uppercase; letter-spacing: 1px;">{{ $title }}</h2>
    </div>
</div>
