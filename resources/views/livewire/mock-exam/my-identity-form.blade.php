<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-lg font-bold text-slate-900 dark:text-white">Exam Identity</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            These details appear at the top of every exam PDF you generate — saved once, reused every time.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 text-sm bg-emerald-50 border border-emerald-200 text-emerald-800" style="border-radius: 2px;">
            {{ session('success') }}
        </div>
    @endif

    @if(empty($fields))
        <div class="p-6 text-sm text-slate-500 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
            No identity fields have been configured yet.
        </div>
    @else
        <form wire:submit.prevent="save" class="space-y-5">
            @foreach($fields as $field)
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ $field->label }}
                        @if($field->is_required)<span class="text-red-500">*</span>@endif
                    </label>

                    @if($field->pretext)
                        <p class="text-xs text-slate-400 dark:text-slate-500 mb-1.5">Shown as: "{{ $field->pretext }} {{ $values[$field->key] ?: '…' }}"</p>
                    @endif

                    @if($field->type === 'image')
                        @if(!empty($values[$field->key]) && empty($pendingUploads[$field->key]))
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($values[$field->key]) }}" class="h-12 w-auto border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                                <button type="button" wire:click="removeImage('{{ $field->key }}')" class="text-xs text-red-600 hover:text-red-800">Remove</button>
                            </div>
                        @endif
                        <input type="file" wire:model="pendingUploads.{{ $field->key }}" accept="image/*" class="text-sm">
                        @error("pendingUploads.{$field->key}") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @else
                        <input type="text" wire:model="values.{{ $field->key }}"
                               class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white" style="border-radius: 2px;">
                        @error("values.{$field->key}") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>
            @endforeach

            <button type="submit" wire:loading.attr="disabled"
                    class="px-4 py-2 text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700" style="border-radius: 2px;">
                <span wire:loading.remove>Save</span>
                <span wire:loading>Saving...</span>
            </button>
        </form>
    @endif
</div>
