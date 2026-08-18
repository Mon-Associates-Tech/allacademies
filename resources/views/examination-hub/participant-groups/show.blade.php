<x-layouts.app>
    <x-examination-hub.navigation active="participant-groups"/>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── BACK LINK ── --}}
        <a href="{{ route('examination-hub.participant-groups.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors uppercase tracking-wider"
           style="letter-spacing: 0.08em;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Groups
        </a>

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #4c1d95, #7c3aed, #a78bfa);"></div>
            <div class="px-7 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ $group->name }}
                    </h1>
                    @if($group->description)
                        <p class="text-sm text-slate-400 mt-1">{{ $group->description }}</p>
                    @endif
                    <p class="text-xs text-slate-500 mt-2 uppercase tracking-wider font-semibold" style="letter-spacing: 0.08em;">
                        {{ $group->members->count() }} {{ Str::plural('participant', $group->members->count()) }}
                    </p>
                </div>
                     <a href="{{ route('examination-hub.participant-groups.edit', $group) }}" 
                         onclick="window.location.href='{{ route('examination-hub.participant-groups.edit', $group) }}'"
                         role="link"
                         class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                         style="background: linear-gradient(135deg, #475569, #64748b); border-radius: 2px; letter-spacing: 0.08em;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Group
                </a>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
                <div class="bg-white dark:bg-slate-900 px-5 py-4 text-sm text-emerald-700 dark:text-emerald-400 font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #991b1b, #dc2626, #f87171);"></div>
                <div class="bg-white dark:bg-slate-900 px-5 py-4 text-sm text-red-700 dark:text-red-400 font-medium flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ── TWO COLUMN LAYOUT ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Add Participant Form --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 overflow-hidden sticky top-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #065f46, #059669); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Add Participant</h2>
                    </div>
                    <form action="{{ route('examination-hub.participant-groups.members.store', $group) }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors"
                                   style="border-radius: 2px;" required>
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors"
                                   style="border-radius: 2px;" required>
                        </div>

                        <div>
                            <label for="unique_code" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Unique Code
                            </label>
                            <input type="text" name="unique_code" id="unique_code" value="{{ old('unique_code') }}"
                                   class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors"
                                   style="border-radius: 2px;" placeholder="Optional">
                        </div>

                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                                style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 2px; letter-spacing: 0.08em;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Participant
                        </button>
                    </form>
                </div>
            </div>

            {{-- Participants List --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Participants</h2>
                        </div>
                        <span class="text-xs font-mono font-medium px-2.5 py-1 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300" style="border-radius: 2px;">
                            {{ $group->members->count() }} Total
                        </span>
                    </div>

                    @if($group->members->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 mx-auto flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No participants yet</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Add participants using the form on the left</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Code</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                    @foreach($group->members as $member)
                                        <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors" id="member-{{ $member->id }}">
                                            <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                                {{ $member->name }}
                                            </td>
                                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                                {{ $member->email }}
                                            </td>
                                            <td class="px-6 py-3.5">
                                                @if($member->unique_code)
                                                    <span class="inline-flex items-center justify-center text-xs font-mono font-medium px-2.5 py-1 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300" style="border-radius: 2px;">
                                                        {{ $member->unique_code }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 dark:text-slate-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3.5 text-right">
                                                <div class="inline-flex items-center gap-3">
                                                    <button onclick='editMember({{ $member->id }}, {!! json_encode($member->name, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}, {!! json_encode($member->email, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}, {!! json_encode($member->unique_code, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!})'
                                                            class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                                                        Edit
                                                    </button>
                                                    <span class="text-slate-300 dark:text-slate-600">|</span>
                                                    <form action="{{ route('examination-hub.participant-groups.members.destroy', [$group, $member]) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Remove this participant?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ── EDIT MODAL ── --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden shadow-2xl" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <h3 class="text-lg font-bold text-white" style="font-family: 'Georgia', serif; letter-spacing: -0.02em;">Edit Participant</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="edit_name" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" 
                           class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors"
                           style="border-radius: 2px;" required>
                </div>

                <div>
                    <label for="edit_email" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="edit_email" 
                           class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors"
                           style="border-radius: 2px;" required>
                </div>

                <div>
                    <label for="edit_unique_code" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Unique Code</label>
                    <input type="text" name="unique_code" id="edit_unique_code" 
                           class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors"
                           style="border-radius: 2px;">
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                            style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px; letter-spacing: 0.08em;">
                        Update
                    </button>
                    <button type="button" onclick="closeEditModal()" 
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700 uppercase tracking-wider"
                            style="border-radius: 2px; letter-spacing: 0.08em;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function editMember(id, name, email, code) {
                const modal = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                
                form.action = "{{ route('examination-hub.participant-groups.members.update', [$group, ':id']) }}".replace(':id', id);
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_unique_code').value = code || '';
                
                modal.classList.remove('hidden');
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
            }
        </script>
    @endpush
</x-layouts.app>