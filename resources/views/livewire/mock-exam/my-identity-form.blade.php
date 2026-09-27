<div class="ei-wrap">
    {{-- Fonts + scoped styles (everything is namespaced .ei-*) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Zilla+Slab:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ---------- palette ---------- */
        .ei-wrap{
            --paper:#f5f3ec;      --surface:#ffffff;   --surface-2:#faf8f1;
            --ink:#1d2534;        --ink-soft:#5a6274;  --muted:#8b909d;
            --line:#e2ddd0;       --line-strong:#c9c3b1;
            --accent:#b23a2a;     --accent-soft:#f6e5e0;
            --primary:#22304f;    --primary-hi:#2d3f68; --on-primary:#f7f6f0;
            --ok:#1e7a4d;         --ok-bg:#e8f4ec;      --ok-line:#bfe0cd;
            --ring:rgba(34,48,79,.18);
            font-family:'Instrument Sans',ui-sans-serif,system-ui,sans-serif;
            color:var(--ink);
            background:
                radial-gradient(900px 420px at 88% -8%, rgba(34,48,79,.10), transparent 62%),
                repeating-linear-gradient(180deg, transparent 0 31px, rgba(29,37,52,.05) 31px 32px),
                var(--paper);
        }
        .dark .ei-wrap{
            --paper:#0e1219;      --surface:#161b26;   --surface-2:#121722;
            --ink:#e9ebf1;        --ink-soft:#aab1c0;  --muted:#7e8596;
            --line:#2a3141;       --line-strong:#3b4356;
            --accent:#e0604c;     --accent-soft:rgba(224,96,76,.14);
            --primary:#e9ebf1;    --primary-hi:#ffffff; --on-primary:#151a26;
            --ok:#4cc08a;         --ok-bg:rgba(76,192,138,.12); --ok-line:rgba(76,192,138,.35);
            --ring:rgba(233,235,241,.22);
            color:var(--ink);
            background:
                radial-gradient(900px 420px at 88% -8%, rgba(96,120,190,.12), transparent 62%),
                repeating-linear-gradient(180deg, transparent 0 31px, rgba(233,235,241,.045) 31px 32px),
                var(--paper);
        }
        /* answer-booklet red margin rule on wide screens */
        @media (min-width:900px){
            .ei-wrap{
                background:
                    linear-gradient(90deg, transparent 0 79px, rgba(178,58,42,.16) 79px 80px, transparent 80px),
                    radial-gradient(900px 420px at 88% -8%, rgba(34,48,79,.10), transparent 62%),
                    repeating-linear-gradient(180deg, transparent 0 31px, rgba(29,37,52,.05) 31px 32px),
                    var(--paper);
            }
            .dark .ei-wrap{
                background:
                    linear-gradient(90deg, transparent 0 79px, rgba(224,96,76,.12) 79px 80px, transparent 80px),
                    radial-gradient(900px 420px at 88% -8%, rgba(96,120,190,.12), transparent 62%),
                    repeating-linear-gradient(180deg, transparent 0 31px, rgba(233,235,241,.045) 31px 32px),
                    var(--paper);
            }
        }

        /* ---------- base ---------- */
        .ei-wrap *{box-sizing:border-box}
        .ei-shell{max-width:1120px;margin:0 auto;padding:52px 24px 84px}
        @media (min-width:900px){ .ei-shell{padding-left:112px} }
        .ei-mono{font-family:'JetBrains Mono',ui-monospace,SFMono-Regular,monospace}
        .ei-display{font-family:'Zilla Slab',Georgia,serif}

        /* ---------- header ---------- */
        .ei-head{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;margin-bottom:30px}
        .ei-eyebrow{font-size:11px;letter-spacing:.14em;color:var(--ink-soft);display:flex;align-items:center;gap:8px;margin:0 0 10px}
        .ei-tick{width:8px;height:8px;background:var(--accent);display:inline-block}
        .ei-title{font-size:clamp(30px,4vw,40px);line-height:1.05;margin:0 0 10px;font-weight:700;letter-spacing:-.01em}
        .ei-sub{margin:0;font-size:14.5px;color:var(--ink-soft);max-width:52ch;line-height:1.55}

        .ei-stamp{flex:0 0 auto;width:92px;height:92px;border:2px solid var(--accent);border-radius:50%;
            display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;
            transform:rotate(8deg);color:var(--accent);position:relative;opacity:.9}
        .ei-stamp::before{content:"";position:absolute;inset:5px;border:1px dashed var(--accent);border-radius:50%;opacity:.7}
        .ei-stamp span{font-family:'JetBrains Mono';font-size:10px;font-weight:600;letter-spacing:.18em}
        .ei-stamp--fresh{animation:ei-stamp .55s cubic-bezier(.2,1.4,.4,1) both}
        @keyframes ei-stamp{0%{transform:rotate(8deg) scale(1.5);opacity:0}60%{opacity:1}100%{transform:rotate(8deg) scale(1);opacity:.9}}

        /* ---------- flash ---------- */
        .ei-flash{display:flex;gap:10px;align-items:flex-start;background:var(--ok-bg);border:1px solid var(--ok-line);
            border-left:3px solid var(--ok);padding:12px 14px;border-radius:4px;font-size:13.5px;margin-bottom:24px;
            animation:ei-in .35s ease both}
        .ei-flash svg{flex:0 0 auto;width:16px;height:16px;color:var(--ok);margin-top:2px}
        .ei-flash strong{color:var(--ok)}
        .ei-flash p{margin:0;line-height:1.45}

        /* ---------- layout ---------- */
        .ei-grid{display:grid;gap:28px;grid-template-columns:1fr;align-items:start}
        @media (min-width:1024px){
            .ei-grid{grid-template-columns:minmax(0,1fr) 350px}
            .ei-preview{position:sticky;top:28px}
        }

        /* ---------- form card ---------- */
        .ei-form{background:var(--surface);border:1px solid var(--line);border-radius:6px;overflow:hidden;
            box-shadow:0 1px 2px rgba(20,24,34,.04),0 18px 40px -30px rgba(20,24,34,.25)}
        .ei-form-head{display:flex;justify-content:space-between;align-items:center;gap:12px;
            padding:14px 22px;border-bottom:1px solid var(--line);background:var(--surface-2)}
        .ei-form-head p{margin:0;font-size:10.5px;letter-spacing:.14em;color:var(--muted)}
        .ei-fields{padding:6px 22px 10px}

        .ei-field{padding:18px 0;border-bottom:1px dashed var(--line)}
        .ei-field:last-child{border-bottom:0}
        .ei-field-head{display:flex;align-items:center;gap:10px;margin-bottom:10px}
        .ei-idx{font-size:10.5px;color:var(--accent);letter-spacing:.08em;background:var(--accent-soft);padding:2px 6px;border-radius:3px}
        .ei-label{font-size:14px;font-weight:600;margin:0;flex:1;color:var(--ink)}
        .ei-req{color:var(--accent);font-weight:700}
        .ei-type{font-size:9.5px;letter-spacing:.14em;color:var(--muted);border:1px solid var(--line-strong);padding:2px 6px;border-radius:3px}

        .ei-pre{margin:0 0 10px;font-size:11px;color:var(--muted)}
        .ei-pre-chip{color:var(--ink-soft);background:var(--surface-2);border:1px solid var(--line);padding:2px 7px;border-radius:3px;margin-left:4px}
        .ei-pre-chip b{color:var(--ink);font-weight:600}

        .ei-input{width:100%;padding:10px 12px;font:inherit;font-size:14px;color:var(--ink);background:var(--surface);
            border:1px solid var(--line-strong);border-radius:4px;transition:border-color .15s ease,box-shadow .15s ease}
        .ei-input::placeholder{color:var(--muted)}
        .ei-input:hover{border-color:var(--ink-soft)}
        .ei-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}

        /* ---------- image field ---------- */
        .ei-thumb{display:flex;align-items:center;gap:14px;padding:10px;border:1px solid var(--line);border-radius:4px;
            background:var(--surface-2);margin-bottom:10px}
        .ei-thumb img{height:46px;width:auto;max-width:120px;object-fit:contain;border:1px solid var(--line);
            background:#fff;border-radius:3px;padding:3px}
        .ei-thumb-tag{display:block;font-size:9.5px;letter-spacing:.14em;color:var(--muted);margin-bottom:4px}
        .ei-remove{font:inherit;font-size:12.5px;font-weight:600;color:var(--accent);background:none;border:0;padding:0;
            cursor:pointer;border-bottom:1px dashed transparent;transition:border-color .15s ease}
        .ei-remove:hover{border-bottom-color:var(--accent)}
        .ei-remove:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
        .ei-pending{font-size:11.5px;color:var(--ok);margin:0 0 10px}

        .ei-file{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0}
        .ei-drop{display:flex;align-items:center;gap:12px;padding:13px 14px;border:1.5px dashed var(--line-strong);
            border-radius:4px;cursor:pointer;color:var(--ink-soft);font-size:13px;
            transition:border-color .15s ease,background .15s ease}
        .ei-drop svg{width:18px;height:18px;color:var(--muted);transition:color .15s ease,transform .15s ease}
        .ei-drop:hover{border-color:var(--primary);background:var(--surface-2)}
        .ei-drop:hover svg{color:var(--primary);transform:translateY(-2px)}
        .ei-drop b{color:var(--ink);font-weight:600}
        .ei-drop-tag{margin-left:auto;font-size:9px;letter-spacing:.14em;color:var(--muted);border:1px solid var(--line);
            padding:3px 6px;border-radius:3px;background:var(--surface)}
        .ei-file:focus-visible + .ei-drop{border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}

        .ei-error{margin:8px 0 0;font-size:11.5px;color:var(--accent)}

        /* ---------- save bar ---------- */
        .ei-savebar{display:flex;justify-content:space-between;align-items:center;gap:16px;
            padding:16px 22px;border-top:1px solid var(--line);background:var(--surface-2)}
        .ei-hint{margin:0;font-size:10px;letter-spacing:.14em;color:var(--muted)}
        .ei-btn{display:inline-flex;align-items:center;justify-content:center;font:inherit;font-size:13.5px;font-weight:600;
            letter-spacing:.02em;color:var(--on-primary);background:var(--primary);border:0;border-radius:4px;padding:10px 20px;
            cursor:pointer;box-shadow:0 1px 0 rgba(20,24,34,.2),0 8px 18px -10px rgba(20,24,34,.5);
            transition:background .15s ease,transform .15s ease}
        .ei-btn:hover{background:var(--primary-hi);transform:translateY(-1px)}
        .ei-btn:active{transform:translateY(0)}
        .ei-btn:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
        .ei-btn[disabled]{opacity:.65;cursor:wait;transform:none}
        .ei-spinner{width:13px;height:13px;border:2px solid rgba(127,127,127,.4);border-top-color:currentColor;
            border-radius:50%;display:inline-block;margin-right:8px;vertical-align:-2px;animation:ei-spin .7s linear infinite}
        @keyframes ei-spin{to{transform:rotate(1turn)}}

        /* ---------- sheet preview ---------- */
        .ei-preview-cap{margin:0 0 10px;font-size:10.5px;letter-spacing:.14em;color:var(--ink-soft)}
        .ei-preview-cap span{color:var(--muted)}
        .ei-paper{background:#fffdf7;color:#232733;border:1px solid #e3dfd0;border-radius:4px;padding-bottom:18px;
            box-shadow:0 1px 0 rgba(31,36,52,.05),0 24px 48px -28px rgba(31,36,52,.35);
            transform:rotate(.4deg);transition:transform .3s ease}
        .ei-paper:hover{transform:rotate(0deg) translateY(-2px)}
        .dark .ei-paper{background:#f6f2e7;border-color:#d8d2bf}
        .ei-band{font-size:8.5px;letter-spacing:.22em;text-align:center;color:#6c6653;background:#f1ecdd;
            border-bottom:1px dashed #d6cfb8;padding:7px 10px}
        .ei-bubbles{display:flex;gap:6px;justify-content:flex-end;padding:8px 14px 0}
        .ei-bubbles i{width:8px;height:8px;border:1px solid #b9b19a;border-radius:50%}
        .ei-bubbles i.on{background:var(--accent);border-color:var(--accent)}
        .ei-paper-head{display:flex;align-items:center;gap:12px;padding:14px 18px 10px}
        .ei-paper-logo{height:44px;width:auto;object-fit:contain}
        .ei-paper-school{margin:0;font-size:17px;font-weight:700;line-height:1.15}
        .ei-paper-kind{margin:2px 0 0;font-size:8.5px;letter-spacing:.3em;color:#8a8264}
        .ei-paper-rule{height:3px;margin:0 18px;background:linear-gradient(#232733 0 1.5px, transparent 1.5px 3px, #232733 3px)}
        .ei-paper-row{display:flex;align-items:baseline;gap:8px;padding:9px 18px 0;font-size:11.5px}
        .ei-paper-label{color:#6d6753;white-space:nowrap}
        .ei-paper-dots{flex:1;border-bottom:1.5px dotted #b6ae97;transform:translateY(-3px)}
        .ei-paper-val{font-weight:600;max-width:55%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .ei-paper-foot{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:18px 18px 0;
            padding-top:12px;border-top:1px solid #e6e0cd}
        .ei-barcode{width:120px;height:26px;
            background:repeating-linear-gradient(90deg,#232733 0 2px,transparent 2px 4px,#232733 4px 5px,transparent 5px 9px,#232733 9px 12px,transparent 12px 14px)}
        .ei-paper-id{font-size:8.5px;letter-spacing:.14em;color:#8a8264}
        .ei-preview-note{margin:12px 2px 0;font-size:12px;color:var(--muted)}

        /* ---------- empty state ---------- */
        .ei-empty{background:var(--surface);border:1.5px dashed var(--line-strong);border-radius:6px;padding:56px 28px;text-align:center}
        .ei-empty-sheet{width:58px;height:74px;background:var(--surface-2);border:1px solid var(--line-strong);
            margin:0 auto 18px;position:relative;box-shadow:0 10px 20px -12px rgba(20,24,34,.3)}
        .ei-empty-sheet::before{content:"";position:absolute;left:10px;right:10px;top:14px;height:2px;
            background:var(--line-strong);box-shadow:0 9px 0 var(--line-strong),0 18px 0 var(--line-strong)}
        .ei-empty h2{font-size:20px;margin:0 0 8px}
        .ei-empty p{margin:0 auto;max-width:44ch;color:var(--ink-soft);font-size:13.5px;line-height:1.6}

        /* ---------- motion ---------- */
        .ei-rise{animation:ei-in .5s ease both}
        @keyframes ei-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
        @media (prefers-reduced-motion:reduce){.ei-wrap *{animation:none!important;transition:none!important}}
        @media (max-width:640px){
            .ei-stamp{width:72px;height:72px}
            .ei-savebar{flex-direction:column;align-items:stretch;text-align:center}
        }
    </style>

    <div class="ei-shell">

        {{-- ============ Header ============ --}}
        <header class="ei-head ei-rise">
            <div>
                <p class="ei-eyebrow ei-mono"><span class="ei-tick"></span>FORM EI-01 · PRINTED AT THE HEAD OF EVERY EXAM PDF</p>
                <h1 class="ei-title ei-display">Exam Identity</h1>
                <p class="ei-sub">Saved once, reused every time — the block you configure below is stamped onto the top of every exam paper you generate.</p>
            </div>
            <div class="ei-stamp {{ session('success') ? 'ei-stamp--fresh' : '' }}" aria-hidden="true">
                <span>EXAM</span>
                <span>IDENTITY</span>
            </div>
        </header>

        {{-- ============ Flash ============ --}}
        @if(session('success'))
            <div class="ei-flash" role="status">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                <p><strong>Saved.</strong> {{ session('success') }}</p>
            </div>
        @endif

        {{-- ============ Empty state ============ --}}
        @if(empty($fields))
            <div class="ei-empty ei-rise">
                <div class="ei-empty-sheet" aria-hidden="true"></div>
                <h2 class="ei-display">No identity fields configured yet</h2>
                <p>Add fields such as institution name, exam code or a logo — they will be printed at the head of every exam PDF you generate.</p>
            </div>
        @else

            <div class="ei-grid">

                {{-- ============ Form ============ --}}
                <form wire:submit.prevent="save" class="ei-form ei-rise" style="animation-delay:.06s">
                    <div class="ei-form-head">
                        <p class="ei-mono">HEADER BLOCK · {{ count($fields) }} FIELD{{ count($fields) === 1 ? '' : 'S' }}</p>
                        <p class="ei-mono"><span class="ei-req">*</span> REQUIRED</p>
                    </div>

                    <div class="ei-fields">
                        @foreach($fields as $field)
                            <div class="ei-field">
                                <div class="ei-field-head">
                                    <span class="ei-idx ei-mono">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <label class="ei-label" for="ei-f-{{ $field->key }}">
                                        {{ $field->label }}
                                        @if($field->is_required)<span class="ei-req" title="Required">*</span>@endif
                                    </label>
                                    @if($field->type === 'image')<span class="ei-type ei-mono">IMG</span>@endif
                                </div>

                                @if($field->pretext)
                                    <p class="ei-pre ei-mono">PRINTS AS
                                        <span class="ei-pre-chip">{{ $field->pretext }} <b>{{ $values[$field->key] ?: '…' }}</b></span>
                                    </p>
                                @endif

                                @if($field->type === 'image')
                                    @if(!empty($values[$field->key]) && empty($pendingUploads[$field->key]))
                                        <div class="ei-thumb">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($values[$field->key]) }}" alt="Current {{ $field->label }}">
                                            <div>
                                                <span class="ei-thumb-tag ei-mono">CURRENT FILE</span>
                                                <button type="button" wire:click="removeImage('{{ $field->key }}')" class="ei-remove">Remove image</button>
                                            </div>
                                        </div>
                                    @elseif(!empty($pendingUploads[$field->key]))
                                        <p class="ei-pending ei-mono">◉ NEW IMAGE SELECTED — APPLIES WHEN YOU SAVE</p>
                                    @endif

                                    <input type="file" id="ei-f-{{ $field->key }}" accept="image/*"
                                           wire:model="pendingUploads.{{ $field->key }}" class="ei-file">
                                    <label for="ei-f-{{ $field->key }}" class="ei-drop">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span><b>{{ !empty($values[$field->key]) ? 'Replace' : 'Upload' }} image</b> — click to browse</span>
                                        <span class="ei-drop-tag ei-mono">HEADER&nbsp;ASSET</span>
                                    </label>
                                    @error("pendingUploads.{$field->key}") <p class="ei-error ei-mono">▲ {{ $message }}</p> @enderror
                                @else
                                    <input type="text" id="ei-f-{{ $field->key }}"
                                           wire:model="values.{{ $field->key }}" class="ei-input">
                                    @error("values.{$field->key}") <p class="ei-error ei-mono">▲ {{ $message }}</p> @enderror
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="ei-savebar">
                        <p class="ei-hint ei-mono">SAVED ONCE · REUSED ON EVERY PAPER</p>
                        <button type="submit" wire:loading.attr="disabled" class="ei-btn">
                            <span wire:loading.remove wire:target="save">Save identity</span>
                            <span wire:loading wire:target="save"><i class="ei-spinner"></i>Saving…</span>
                        </button>
                    </div>
                </form>

                {{-- ============ Sheet preview ============ --}}
                <aside class="ei-preview ei-rise" style="animation-delay:.12s">
                    <p class="ei-preview-cap ei-mono">SHEET PREVIEW <span>· REFLECTS SAVED VALUES</span></p>

                    <div class="ei-paper" aria-hidden="true">
                        <div class="ei-band ei-mono">CONFIDENTIAL — DO NOT TURN OVER UNTIL INSTRUCTED</div>
                        <div class="ei-bubbles"><i></i><i></i><i></i><i class="on"></i><i></i><i></i></div>

                        <div class="ei-paper-head">
                            @php($imgField = collect($fields)->first(fn ($f) => $f->type === 'image'))
                            @if($imgField && !empty($values[$imgField->key]))
                                <img class="ei-paper-logo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($values[$imgField->key]) }}" alt="">
                            @endif
                            <div>
                                @php($nameField = collect($fields)->first(fn ($f) => $f->type !== 'image' && !empty($values[$f->key] ?? '')))
                                <p class="ei-paper-school ei-display">{{ $nameField ? $values[$nameField->key] : 'Your institution' }}</p>
                                <p class="ei-paper-kind ei-mono">EXAMINATION PAPER</p>
                            </div>
                        </div>

                        <div class="ei-paper-rule"></div>

                        @foreach($fields as $pfield)
                            @continue($pfield->type === 'image')
                            <div class="ei-paper-row">
                                <span class="ei-paper-label">{{ $pfield->pretext ?: $pfield->label }}</span>
                                <span class="ei-paper-dots"></span>
                                <span class="ei-paper-val">{{ $values[$pfield->key] ?: '···········' }}</span>
                            </div>
                        @endforeach

                        <div class="ei-paper-foot">
                            <span class="ei-barcode"></span>
                            <span class="ei-paper-id ei-mono">EI-01 · AUTO-FILLED AT GENERATION</span>
                        </div>
                    </div>

                    <p class="ei-preview-note">This is how the identity block sits on each generated PDF.</p>
                </aside>

            </div>
        @endif

    </div>
</div>
