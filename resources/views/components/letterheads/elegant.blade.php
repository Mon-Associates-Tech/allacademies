@props(['school', 'title' => 'Document'])

<div style="padding: 25px; background: #f9fafb; border: 2px solid #d1d5db; border-radius: 8px; margin-bottom: 30px;">
    <div style="display: flex; gap: 25px; align-items: start;">
        @if($school->logo)
            <div style="min-width: 90px;">
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                     style="width: 90px; height: 90px; object-fit: contain; border: 1px solid #e5e7eb; padding: 8px; background: white; border-radius: 4px;">
            </div>
        @endif
        <div style="flex: 1;">
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin: 0 0 10px; font-family: 'Georgia', serif;">
                {{ $school->name }}
            </h1>
            <div style="font-size: 13px; color: #4b5563; line-height: 1.6;">
                <p style="margin: 0;"><strong>Address:</strong> {{ $school->address }}, {{ $school->city }}, {{ $school->state }} {{ $school->postal_code }}</p>
                <p style="margin: 5px 0 0;"><strong>Contact:</strong> {{ $school->phone }} | {{ $school->email }}</p>
                @if($school->website)
                    <p style="margin: 5px 0 0;"><strong>Web:</strong> {{ $school->website }}</p>
                @endif
            </div>
        </div>
    </div>
    <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #9ca3af;">
        <h2 style="font-size: 20px; font-weight: 600; color: #374151; margin: 0; font-family: 'Georgia', serif;">{{ $title }}</h2>
    </div>
</div>
