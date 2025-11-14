@props(['school', 'title' => 'Document'])

<div style="background: #1e293b; color: white; padding: 25px; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 20px;">
            @if($school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                     style="width: 70px; height: 70px; object-fit: contain; background: white; padding: 8px; border-radius: 4px;">
            @endif
            <div>
                <h1 style="font-size: 28px; font-weight: 700; margin: 0; letter-spacing: -0.5px;">{{ $school->name }}</h1>
                <p style="font-size: 14px; color: #cbd5e1; margin: 5px 0 0;">Excellence in Education</p>
            </div>
        </div>
        <div style="text-align: right; font-size: 12px; color: #cbd5e1;">
            <p style="margin: 0;"><strong>Phone:</strong> {{ $school->phone }}</p>
            <p style="margin: 3px 0;"><strong>Email:</strong> {{ $school->email }}</p>
            @if($school->website)
                <p style="margin: 3px 0;"><strong>Web:</strong> {{ $school->website }}</p>
            @endif
        </div>
    </div>
    <div style="background: #0ea5e9; padding: 10px 20px; margin: 0 -25px; text-align: center;">
        <h2 style="font-size: 20px; font-weight: 600; margin: 0; text-transform: uppercase; letter-spacing: 1px;">{{ $title }}</h2>
    </div>
</div>
