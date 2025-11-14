@props(['school', 'title' => 'Document'])

<div style="position: relative; padding: 25px; margin-bottom: 30px; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 8px; background: linear-gradient(90deg, #f43f5e, #f59e0b, #10b981, #3b82f6, #8b5cf6);"></div>
    
    <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
        @if($school->logo)
            <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                 style="width: 85px; height: 85px; object-fit: contain; border: 3px solid #f59e0b; border-radius: 50%; padding: 5px; background: white;">
        @endif
        <div style="flex: 1;">
            <h1 style="font-size: 28px; font-weight: 800; color: #1e40af; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
                {{ $school->name }}
            </h1>
            <p style="font-size: 14px; color: #059669; font-weight: 600; margin: 8px 0 5px;">
                🏛️ {{ $school->address }}, {{ $school->city }}, {{ $school->state }}
            </p>
            <p style="font-size: 13px; color: #6366f1; margin: 0;">
                📞 {{ $school->phone }} | 📧 {{ $school->email }}
                @if($school->website) | 🌐 {{ $school->website }}@endif
            </p>
        </div>
    </div>
    
    <div style="margin-top: 20px; padding: 12px; background: linear-gradient(135deg, #fef3c7, #ddd6fe); border-radius: 6px; text-align: center;">
        <h2 style="font-size: 20px; font-weight: 700; color: #7c3aed; margin: 0; text-transform: uppercase;">{{ $title }}</h2>
    </div>
</div>
