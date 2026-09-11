@props(['section'])

@if($section->insert_blank_page)
    <div class="page-break" style="height: 267mm; width: 100%; display: table;">
        <div style="display: table-cell; vertical-align: middle; text-align: center;">
            <h2 style="font-size: 16pt; font-weight: bold;">{{ $section->blank_page_text ?: 'Do not turn the next page until you are told to do so' }}</h2>
        </div>
    </div>
@endif

@if($section->hasAttachment())
    @if($section->isTextAttachment())
        <div class="page-break" style="white-space: pre-wrap; font-size: 10pt; line-height: 1.5; padding: 10px;">
            {{ $section->attachment_text }}
        </div>
    @elseif($section->isPdfAttachment())
        @foreach(($section->attachment_pdf_images ?? []) as $imagePath)
            <div class="page-break">
                <img src="{{ public_path('storage/'.$imagePath) }}" style="width: 100%;">
            </div>
        @endforeach
    @elseif($section->isImageAttachment())
        <div class="page-break" style="text-align: center;">
            <img src="{{ public_path('storage/'.$section->attachment_image_path) }}" style="max-width: 100%;">
        </div>
    @endif
@endif