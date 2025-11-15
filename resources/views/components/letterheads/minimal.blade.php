
@props(['school', 'title' => 'Document'])

<div style="padding: 20px; border-bottom: 3px solid #1e40af; margin-bottom: 30px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div style="flex: 1;">
            @if($school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                     style="max-width: 100px; max-height: 100px; margin-bottom: 10px;">
            @endif
        </div>
        <div style="flex: 2; text-align: center;">
            <h1 style="font-size: 28px; font-weight: bold; color: #1e40af; margin: 0;">{{ $school->name }}</h1>
            <p style="font-size: 14px; color: #64748b; margin: 5px 0;">{{ $school->address }}, {{ $school->city }}</p>
            <p style="font-size: 14px; color: #64748b; margin: 5px 0;">
                <strong>Phone:</strong> {{ $school->phone }} | <strong>Email:</strong> {{ $school->email }}
            </p>
            @if($school->website)
                <p style="font-size: 14px; color: #64748b; margin: 5px 0;"><strong>Website:</strong> {{ $school->website }}</p>
            @endif
        </div>
        <div style="flex: 1;"></div>
    </div>
    <div style="text-align: center; margin-top: 15px;">
        <h2 style="font-size: 20px; font-weight: 600; color: #334155; margin: 0;">{{ $title }}</h2>
    </div>
</div>
