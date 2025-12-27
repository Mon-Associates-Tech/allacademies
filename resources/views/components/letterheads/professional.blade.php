@props(['school', 'title' => 'Document'])

<div style="border: 4px solid #10b981; padding: 20px; margin-bottom: 30px;">
    <div style="border: 1px solid #6ee7b7; padding: 15px;">
        <div style="text-align: center;">
            @if($school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                     style="max-width: 85px; max-height: 85px; margin-bottom: 12px;">
            @endif
            <h1 style="font-size: 28px; font-weight: 700; color: #065f46; margin: 0 0 8px; text-transform: uppercase;">
                {{ $school->name }}
            </h1>
            <div style="height: 2px; width: 80px; background: #10b981; margin: 10px auto;"></div>
            <p style="font-size: 13px; color: #047857; margin: 8px 0;">
                {{ $school->address }}, {{ $school->city }}, {{ $school->state }}
            </p>
            <p style="font-size: 13px; color: #047857; margin: 5px 0;">
                Phone: {{ $school->phone }} | Email: {{ $school->email }}
            </p>
            @if($school->website)
                <p style="font-size: 13px; color: #047857; margin: 5px 0;">Website: {{ $school->website }}</p>
            @endif
        </div>
        <div style="text-align: center; margin-top: 18px; padding-top: 15px; border-top: 2px dashed #6ee7b7;">
            <h2 style="font-size: 20px; font-weight: 600; color: #065f46; margin: 0;">{{ $title }}</h2>
        </div>
    </div>
</div>
