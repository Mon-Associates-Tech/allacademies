{{--
    Shared front-page (cover) partial for mock-exam PDFs.

    Renders a template's front_page_config blocks (heading, richtext, image,
    divider, info_table) as a single vertically-centred cover page. Used by
    both mock-exam.pdf.exam and mock-exam.pdf.subject-exam so there is one
    place to fix bugs / restyle instead of two drifting copies.

    Expected variables:
      $blocks          array   Ordered list of front-page blocks. If empty,
                                this partial renders nothing.
      $fieldValues     array   Associative field_key => display value for any
                                info_table block (e.g. ['date' => ..., 'duration'
                                => ..., 'subject' => ...]). Fields not present
                                here render as a blank fillable line.
      $fontSize        float   Base document font size in pt.
      $frontPageHeight string  CSS length matching the caller's @page content
                                height (A4 height minus its own @page top/bottom
                                margins), so the cover fills exactly one page
                                regardless of how many blocks it has.
--}}
<style>
    /* ─── Front Page (Cover) ─── */
    .front-page {
        page-break-after: always;
        font-family: 'Georgia', 'Times New Roman', serif;
        color: #111;
    }
    .front-page-frame {
        display: table;
        width: 100%;
        height: {{ $frontPageHeight ?? '267mm' }};
    }
    .front-page-content {
        display: table-cell;
        vertical-align: middle;
        text-align: center;
    }
    .fp-block { margin-bottom: 10mm; }
    .fp-block:last-child { margin-bottom: 0; }

    /* Headings */
    .fp-heading-h1 {
        font-size: {{ ($fontSize ?? 11) + 9 }}pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #111;
        line-height: 1.4;
    }
    .fp-heading-h2 {
        font-size: {{ ($fontSize ?? 11) + 4 }}pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #111;
        line-height: 1.4;
    }
    .fp-heading-h3 {
        font-size: {{ ($fontSize ?? 11) + 1 }}pt;
        font-style: italic;
        color: #666;
        font-family: Arial, Helvetica, sans-serif;
        line-height: 1.4;
    }

    /* Rich text */
    .fp-richtext {
        max-width: 140mm;
        margin: 0 auto;
        text-align: left;
        font-size: {{ $fontSize ?? 11 }}pt;
        line-height: 1.65;
        color: #333;
    }

    /* Image */
    .fp-image {
        display: inline-block;
        max-width: 100%;
    }

    /* Divider — reuses the same heavy/light double-rule motif as the
       document header elsewhere in these PDFs, instead of a plain bar. */
    .fp-divider .rule-heavy { height: 3px; background: #111; }
    .fp-divider .rule-light { height: 1px; background: #111; margin: 3px 0; }

    /* Info table → candidate declaration fields, matching the ruled
       field-line style used in the Candidate Information section rather
       than a boxed spreadsheet-style table. */
    .fp-fields {
        max-width: 150mm;
        margin: 0 auto;
        text-align: left;
    }
    .fp-field-row {
        display: table;
        width: 100%;
        margin-bottom: 14px;
    }
    .fp-field-row:last-child { margin-bottom: 0; }
    .fp-field-cell {
        display: table-cell;
        padding-right: 24px;
        vertical-align: bottom;
    }
    .fp-field-cell:last-child { padding-right: 0; }
    .fp-field-lbl {
        display: block;
        font-size: 7pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #777;
        font-family: Arial, Helvetica, sans-serif;
        margin-bottom: 13px;
    }
    .fp-field-answer {
        border-bottom: 1px solid #444;
        min-height: 1em;
        font-size: {{ $fontSize ?? 11 }}pt;
        font-weight: bold;
        color: #111;
        padding-bottom: 2px;
    }
</style>

@if(!empty($blocks))
    <div class="front-page">
        <div class="front-page-frame">
            <div class="front-page-content">
                @foreach($blocks as $block)
                    <div class="fp-block">
                        @switch($block['type'])

                            @case('heading')
                                <div class="fp-heading-{{ $block['level'] ?? 'h2' }}">
                                    {{ $block['content'] ?? '' }}
                                </div>
                                @break

                            @case('richtext')
                                <div class="fp-richtext">
                                    {!! $block['content'] ?? '' !!}
                                </div>
                                @break

                            @case('image')
                                @php
                                    // 'src' is already a ready-to-use URL by the time it reaches
                                    // this view — the Front Page Builder resolves uploads via
                                    // Storage::url() before saving, and URL-type blocks store the
                                    // pasted URL directly. Do not re-resolve it here (that
                                    // double-prefixed uploaded images in the old markup).
                                    $fpImgSrc = $block['src'] ?? null;
                                    $fpImgAlign = $block['alignment'] ?? 'center';
                                @endphp
                                @if($fpImgSrc)
                                    <div style="text-align: {{ $fpImgAlign }};">
                                        <img src="{{ $fpImgSrc }}"
                                             class="fp-image"
                                             style="width: {{ $block['width'] ?? 200 }}px;"
                                             alt="{{ $block['alt'] ?? '' }}">
                                    </div>
                                @endif
                                @break

                            @case('divider')
                                <div class="fp-divider">
                                    <div class="rule-heavy"></div>
                                    <div class="rule-light"></div>
                                </div>
                                @break

                            @case('info_table')
                                @php
                                    // Keys mirror those produced by the Front Page Builder's
                                    // info_table block (see front-page-builder.blade.php
                                    // $infoFields array). Only fields the caller could compute
                                    // (date, duration, subject, ...) get a value via
                                    // $fieldValues; everything else (name, index no.,
                                    // signature, score, grade) renders as a blank fillable line.
                                    $fpFieldLabels = [
                                        'candidate_name' => 'Full Name',
                                        'index_number'   => 'Index Number',
                                        'date'           => 'Date',
                                        'duration'       => 'Duration',
                                        'subject'        => 'Subject',
                                        'grade'          => 'Grade / Class',
                                        'signature'      => 'Invigilator Signature',
                                        'score'          => 'Total Score',
                                    ];
                                    $fpActiveFields = collect($block['fields'] ?? []);
                                @endphp
                                @if($fpActiveFields->isNotEmpty())
                                    <div class="fp-fields">
                                        @foreach($fpActiveFields->chunk(2) as $fpRow)
                                            <div class="fp-field-row">
                                                @foreach($fpRow as $fpFieldKey)
                                                    <div class="fp-field-cell">
                                                        <span class="fp-field-lbl">{{ $fpFieldLabels[$fpFieldKey] ?? $fpFieldKey }}</span>
                                                        <div class="fp-field-answer">{{ ($fieldValues ?? [])[$fpFieldKey] ?? '' }}&nbsp;</div>
                                                    </div>
                                                @endforeach
                                                @if($fpRow->count() === 1)
                                                    <div class="fp-field-cell"></div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @break
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif