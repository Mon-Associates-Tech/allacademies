@props(['school', 'title' => 'Document'])

<div style="background: linear-gradient(to bottom, #111827 0%, #1f2937 100%); color: white; padding: 30px; margin-bottom: 30px; position: relative;">
    <div style="position: absolute; top: 0; right: 0; width: 200px; height: 200px; background: radial-gradient(circle, rgba(59,130,246,0.2), transparent); border-radius: 50%;"></div>
    
    <div style="position: relative; z-index: 1;">
        <div style="display: flex; align-items: center; gap: 25px; margin-bottom: 20px;">
            @if($school->logo)
                <img src="{{ public_path('storage/' . $school->logo) }}" alt="{{ $school->name }}" 
                     style="width: 95px; height: 95px; object-fit: contain; background: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            @endif
            <div>
                <h1 style="font-size: 32px; font-weight: 700; margin: 0; letter-spacing: -1px;">{{ $school->name }}</h1>
                <p style="font-size: 15px; color: #60a5fa; margin: 8px 0 0; font-weight: 500;">Institution of Excellence</p>
            </div>
        </div>
        
        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #cbd5e1; padding: 15px; background: rgba(31, 41, 55, 0.5); border-radius: 6px; margin-bottom: 20px;">
            <div>
                <p style="margin: 0;"><strong style="color: #93c5fd;">Address:</strong> {{ $school->address }}, {{ $school->city }}</p>
                <p style="margin: 5px 0 0;"><strong style="color: #93c5fd;">State:</strong> {{ $school->state }} {{ $school->postal_code }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0;"><strong style="color: #93c5fd;">Phone:</strong> {{ $school->phone }}</p>
                <p style="margin: 5px 0 0;"><strong style="color: #93c5fd;">Email:</strong> {{ $school->email }}</p>
                @if($school->website)
                    <p style="margin: 5px 0 0;"><strong style="color: #93c5fd;">Web:</strong> {{ $school->website }}</p>
                @endif
            </div>
        </div>
        
        <div style="background: linear-gradient(90deg, #3b82f6, #8b5cf6); padding: 12px 20px; margin: 0 -30px; text-align: center;">
            <h2 style="font-size: 22px; font-weight: 600; margin: 0; text-transform: uppercase; letter-spacing: 2px;">{{ $title }}</h2>
        </div>
    </div>
</div>
