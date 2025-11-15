@props(['school', 'title' => 'Document'])

<div style="padding: 20px 0; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: start; padding-bottom: 15px; border-bottom: 4px solid #0891b2;">
        <div style="flex: 1;">
            @if($school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                     style="max-width: 100px; max-height: 100px;">
            @endif
        </div>
        <div style="flex: 2; text-align: right;">
            <h1 style="font-size: 26px; font-weight: 700; color: #0e7490; margin: 0 0 10px; text-transform: uppercase; letter-spacing: 1px;">
                {{ $school->name }}
            </h1>
            <div style="font-size: 12px; color: #475569; line-height: 1.7;">
                <p style="margin: 0;">{{ $school->address }}</p>
                <p style="margin: 0;">{{ $school->city }}, {{ $school->state }} {{ $school->postal_code }}</p>
                <p style="margin: 5px 0 0;">
                    <strong>T:</strong> {{ $school->phone }} | 
                    <strong>E:</strong> {{ $school->email }}
                </p>
                @if($school->website)
                    <p style="margin: 2px 0 0;"><strong>W:</strong> {{ $school->website }}</p>
                @endif
            </div>
        </div>
    </div>
    <div style="margin-top: 20px;">
        <h2 style="font-size: 22px; font-weight: 600; color: #0e7490; margin: 0; padding: 10px; background: #e0f2fe; border-left: 4px solid #0891b2;">
            {{ $title }}
        </h2>
    </div>
</div>
