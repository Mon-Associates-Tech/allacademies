<div>
<x-exam.header
    title=""
    sub="Saved once, reused every time — the block you configure below is stamped onto the top of every exam paper you generate."
    :lines="['MY', 'IDENTITY']"
    :fresh="(bool) session('success')"
/>

<x-exam.flash />

@php
    $hasAnyFields = !empty($fields) || !empty($readonlyFields);
@endphp
@if(empty($hasAnyFields))
    <x-exam.empty
        title="No identity fields configured yet"
        description="Your administrator hasn't added any fields yet. Come back soon — you'll be able to fill in details like your school name, exam code, or logo."
    />
@else
    @php
        $allFields = array_merge($readonlyFields, $fields);
        // For image fields: readonly uses admin_value, user fields use $values
        $imgField = collect($allFields)->first(fn ($f) => $f->type === 'image');
        $imgUrl = null;
        if ($imgField) {
            if ($imgField->is_readonly && $imgField->admin_value) {
                $imgUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($imgField->admin_value);
            } elseif (!empty($values[$imgField->key])) {
                $imgUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($values[$imgField->key]);
            }
        }
        // For name: prefer user-provided text, fall back to readonly text
        $nameField = collect($allFields)->first(fn ($f) => $f->type !== 'image' && !empty($values[$f->key] ?? ''));
        if (!$nameField) {
            $nameField = collect($readonlyFields)->first(fn ($f) => $f->type === 'text' && !empty($f->admin_value));
        }
        $nameValue = $nameField
            ? ($nameField->is_readonly ? $nameField->admin_value : ($values[$nameField->key] ?? ''))
            : 'Your institution';
    @endphp

    <div class="grid grid-cols-1 items-start gap-7 lg:grid-cols-[minmax(0,1fr)_350px]">

        {{-- Form --}}
        <form wire:submit.prevent="save" class="animate-rise [animation-delay:60ms]">
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">HEADER BLOCK · {{ count($fields) }} FIELD{{ count($fields) === 1 ? '' : 'S' }}</p>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="font-bold text-accent">*</span> REQUIRED</p>
                </x-slot:header>

                <div class="px-5">
                    {{-- Read-only fields provided by admin --}}
                    @if(!empty($readonlyFields))
                        <div class="mb-4 rounded-lg border border-dashed border-line bg-surface-2/50 p-4">
                            <p class="mb-3 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                                PROVIDED BY YOUR ADMINISTRATOR
                            </p>
                            @foreach($readonlyFields as $field)
                                @if($field->type === 'image' && $field->admin_value)
                                    <div class="mb-3 last:mb-0">
                                        <p class="text-sm font-medium text-ink">{{ $field->label }}</p>
                                        @if($field->pretext)
                                            <p class="text-xs text-muted">{{ $field->pretext }}</p>
                                        @endif
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($field->admin_value) }}" class="mt-1 h-16 rounded border border-line" />
                                    </div>
                                @elseif($field->type === 'text' && $field->admin_value)
                                    <div class="mb-3 last:mb-0">
                                        <p class="text-xs text-muted">{{ $field->label }}</p>
                                        @if($field->pretext)
                                            <p class="text-xs text-muted">{{ $field->pretext }}</p>
                                        @endif
                                        <p class="text-sm font-medium text-ink">{{ $field->admin_value }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @foreach($fields as $field)
                        @php($fid = 'ei-f-' . $field->key)

                        <x-exam.field
                            :index="$loop->iteration"
                            :label="$field->label"
                            :for="$fid"
                            :required="$field->is_required"
                            :tag="$field->type === 'image' ? 'IMG' : null"
                            :pretext="$field->pretext"
                            :preview="$values[$field->key] ?? ''"
                        >
                            @if($field->type === 'image')
                                <x-exam.dropzone
                                    :id="$fid"
                                    :model="'pendingUploads.' . $field->key"
                                    :current="(!empty($values[$field->key]) && empty($pendingUploads[$field->key]))
                                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($values[$field->key])
                                        : null"
                                    :pending="!empty($pendingUploads[$field->key])"
                                    remove-action="removeImage"
                                    :remove-key="$field->key"
                                />
                            @else
                                <x-exam.input :id="$fid" wire:model="values.{{ $field->key }}" />
                            @endif
                        </x-exam.field>
                    @endforeach
                </div>

                <x-slot:footer>
                    <x-exam.savebar hint="SAVED ONCE · REUSED ON EVERY PAPER">
                        <x-exam.button target="save" loading="Saving…">Save identity</x-exam.button>
                    </x-exam.savebar>
                </x-slot:footer>
            </x-exam.card>
        </form>

        {{-- Sheet preview --}}
        <aside class="animate-rise [animation-delay:120ms] lg:sticky lg:top-7">
            <p class="mb-2.5 font-mono text-[10.5px] tracking-[0.14em] text-ink-soft">
                SHEET PREVIEW <span class="text-muted">· REFLECTS SAVED VALUES</span>
            </p>

            <x-exam.sheet
                :logo="$imgUrl"
                :title="$nameValue"
            >
                @foreach($readonlyFields as $pfield)
                    @continue($pfield->type === 'image')
                    @continue(empty($pfield->admin_value))
                    <x-exam.sheet.row
                        :label="$pfield->pretext ?: $pfield->label"
                        :value="$pfield->admin_value"
                    />
                @endforeach
                @foreach($fields as $pfield)
                    @continue($pfield->type === 'image')
                    <x-exam.sheet.row
                        :label="$pfield->pretext ?: $pfield->label"
                        :value="$values[$pfield->key] ?? ''"
                    />
                @endforeach
            </x-exam.sheet>
        </aside>

    </div>
@endif
</div>