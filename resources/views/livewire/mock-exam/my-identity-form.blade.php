<div>
<x-exam.header
    eyebrow="FORM EI-01 · MY PRINTED BLOCK"
    title="Exam Identity"
    sub="Saved once, reused every time — the block you configure below is stamped onto the top of every exam paper you generate."
    :lines="['MY', 'IDENTITY']"
    :fresh="(bool) session('success')"
/>

<x-exam.flash />

@if(empty($fields))
    <x-exam.empty
        title="No identity fields configured yet"
        description="Your administrator hasn't added any fields yet. Come back soon — you'll be able to fill in details like your school name, exam code, or logo."
    />
@else
    @php
        $imgField  = collect($fields)->first(fn ($f) => $f->type === 'image');
        $nameField = collect($fields)->first(fn ($f) => $f->type !== 'image' && !empty($values[$f->key] ?? ''));
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
                :logo="$imgField && !empty($values[$imgField->key])
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($values[$imgField->key])
                    : null"
                :title="$nameField ? $values[$nameField->key] : 'Your institution'"
            >
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