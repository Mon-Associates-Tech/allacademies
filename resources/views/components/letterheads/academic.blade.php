@props(['school', 'title' => 'Document'])

<div style="padding: 20px; border: 3px double #7c3aed; margin-bottom: 30px;">
    <div style="text-align: center; padding-bottom: 15px; border-bottom: 1px solid #c4b5fd;">
        @if($school->logo)
            <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                 style="max-width: 90px; max-height: 90px; margin-bottom: 12px;">
        @endif
        <h1 style="font-size: 30px; font-weight: 700; color: #5b21b6; margin: 0; font-family: 'Times New Roman', serif;">
            {{ strtoupper($school->name) }}
        </h1>
        <p style="font-size: 14px; color: #7c3aed; font-style: italic; margin: 8px 0;">
            Inspiring Minds, Shaping Futures
        </p>
    </div>
    <div style="display: flex; justify-content: space-around; padding: 12px 0; font-size: 12px; color: #6b7280;">
        <div><strong>📍</strong> {{ $school->address }}, {{ $school->city }}</div>
        <div><strong>📞</strong> {{ $school->phone }}</div>
        <div><strong>✉</strong> {{ $school->email }}</div>
    </div>
    <div style="text-align: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #c4b5fd;">
        <h2 style="font-size: 20px; font-weight: 600; color: #6d28d9; margin: 0; font-family: 'Times New Roman', serif;">
            {{ $title }}
        </h2>
    </div>
</div>
