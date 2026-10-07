<x-exam.page>
    <x-exam.header
        eyebrow="STUDENT RECORDS · PERSONAL FILE"
        title="Student Profile"
        sub="Your personal details, fee records, and academic activity in one place."
        :lines="['STUDENT', 'ID']"
    />

    {{-- ============ Identity card ============ --}}
    <x-exam.card class="mb-6" :padded="false">
        <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                @if($currentAvatar)
                    <img src="{{ $currentAvatar }}" alt="{{ $name ?: 'Student' }}" class="h-20 w-20 rounded-md object-cover ring-2 ring-line">
                @else
                    <div class="grid h-20 w-20 place-items-center rounded-md border border-dashed border-line-strong bg-surface-2 font-display text-2xl font-bold text-muted">
                        {{ strtoupper(substr($name ?: 'S', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="font-display text-2xl font-bold leading-tight text-ink">{{ $name ?: 'Student Profile' }}</h2>
                    <p class="mt-1 font-mono text-[10.5px] tracking-wider text-muted">
                        @if($studentGroup){{ strtoupper($studentGroup->name) }} · @endif
                        MEMBER SINCE {{ strtoupper($student?->created_at?->format('M Y') ?? '—') }}
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                {{-- VERIFY: handler names (editProfile / cancelEdit) --}}
                @if($isEditing)
                    <x-exam.button type="button" variant="ghost" wire:click="toggleEdit">Cancel Changes</x-exam.button>
                @else
                    <x-exam.button type="button" wire:click="toggleEdit">Edit Profile</x-exam.button>
                @endif
            </div>
        </div>
    </x-exam.card>

    <x-exam.flash />
    <x-exam.flash type="error" />

    {{-- ============ Stats ============ --}}
    @if(!empty($profileStats))
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-exam.stat label="Total Assessments" :value="$profileStats['total_assessments']" />
            <x-exam.stat label="Average Score" :value="$profileStats['average_score'] . '%'" tone="primary" />
            <x-exam.stat label="This Month" :value="$profileStats['this_month_assessments']" tone="ok" />
            <x-exam.stat label="Member Since" :value="$profileStats['member_since']" />
        </div>
    @endif

    {{-- ============ School fees ============ --}}
    <x-exam.card class="mb-6" :padded="false">
        <x-slot:header>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SCHOOL FEES PAYMENT DETAILS</p>
            <a href="{{ route('feepayment.form', $student->id) }}"
               class="inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-4 py-2 text-[12.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi">
                Pay Fees
            </a>
        </x-slot:header>

        @if(isset($debugCounts))
            <div class="border-b border-line bg-accent/5 px-5 py-2.5 font-mono text-[10px] tracking-wide text-accent">
                DEBUG · FEE_PAYMENTS={{ $debugCounts['fee_payments'] }} · SCHOOL_PAYMENTS={{ $debugCounts['school_payments'] }} · STUDENT_PAYMENT_RECORDS={{ $debugCounts['student_payment_records'] }} · PAYMENT_HISTORY={{ $debugCounts['payment_history'] }}
            </div>
        @endif

        @if($feeDetails)
         @php
         $feeStatus = strtolower($feeDetails->status ?? 'pending')
         @endphp
            <div class="p-5">
                <p class="mb-2.5 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Current Term Fees</p>
               
                <x-exam.table :headers="['Academic Group', 'Academic Level', 'Total Amount', 'Paid', 'Remaining', 'Status', 'Payment Method']">
                    <tr class="transition hover:bg-surface-2/60">
                        <td class="px-4 py-3 text-sm text-ink">{{ $student->academicGroup->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-ink">{{ $student->academicLevel->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-ink-soft">₵{{ number_format($feeDetails->term_total_amount ?? $feeDetails->amount ?? 0, 2) }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-ink-soft">₵{{ number_format($feeDetails->total_paid ?? 0, 2) }}</td>
                        <td class="px-4 py-3 font-mono text-xs font-semibold {{ ($feeDetails->remaining ?? 0) > 0 ? 'text-accent' : 'text-ok' }}">
                            ₵{{ number_format($feeDetails->remaining ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            <x-exam.badge :tone="match (true) { $feeStatus === 'paid' => 'ok', $feeStatus === 'partial' => 'warn', default => 'muted' }">
                                {{ ucfirst($feeDetails->status ?? 'Pending') }}
                            </x-exam.badge>
                        </td>
                        <td class="px-4 py-3 text-xs text-ink-soft">{{ $feeDetails->payment_method ?? 'Momo' }}</td>
                    </tr>
                </x-exam.table>
            </div>
        @endif
    </x-exam.card>

    {{-- ============ Recent charges & payments ============ --}}
    @if($paymentHistory->isNotEmpty())
        <x-exam.card class="mb-6" :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">RECENT CHARGES & PAYMENTS</p>
                <a href="{{ route('students.payments.index') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">MORE →</a>
            </x-slot:header>

            <x-exam.table :headers="['Amount', 'Currency', 'Status', 'Payer', 'Term', 'Group', 'Level', 'Date', 'Action']">
                @foreach($paymentHistory as $payment)
                    @php
                        $pStatus = strtolower($payment->status ?? 'pending');
                        $isPaid = in_array($pStatus, ['completed', 'succeeded', 'paid']);
                    @endphp

                    <tr class="transition hover:bg-surface-2/60">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-ink">₵{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-3 font-mono text-[11px] text-muted">{{ $payment->currency }}</td>
                        <td class="px-4 py-3">
                            <x-exam.badge :tone="$isPaid ? 'ok' : ($pStatus === 'failed' ? 'danger' : 'warn')">
                                {{ ucfirst($payment->status ?? 'Pending') }}
                            </x-exam.badge>
                        </td>
                        <td class="px-4 py-3 text-xs text-ink-soft">{{ $payment->payer_name }}</td>
                        <td class="px-4 py-3 text-xs text-ink-soft">{{ $payment->term }}</td>
                        <td class="px-4 py-3 text-xs text-ink-soft">{{ $payment->academic_group }}</td>
                        <td class="px-4 py-3 text-xs text-ink-soft">{{ $payment->academic_level }}</td>
                        <td class="px-4 py-3 font-mono text-[11px] text-muted">{{ \Illuminate\Support\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            @if(!$isPaid)
                                @if($payment->type === 'student_record')
                                    <a href="{{ route('students.fees.payment', ['record' => $payment->id]) }}"
                                       class="border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary transition hover:border-current hover:text-primary-hi">PAY</a>
                                @else
                                    <a href="{{ route('students.payments.index') }}"
                                       class="border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary transition hover:border-current hover:text-primary-hi">PAY</a>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-exam.table>
        </x-exam.card>
    @else
        <x-exam.empty compact class="mb-6" title="No payment history found" />
    @endif

    {{-- ============ Details grid ============ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_350px]">

        {{-- Personal information --}}
        {{-- VERIFY: wire:model names + save/cancelEdit handlers (inferred from @error keys) --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PERSONAL INFORMATION</p>
                @if(!$isEditing)
                    <p class="hidden font-mono text-[10px] tracking-wider text-muted sm:block">READ-ONLY · USE EDIT PROFILE</p>
                @endif
            </x-slot:header>

            <form wire:submit.prevent="updateProfile">
                <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                    <x-exam.field bare label="Full Name" required>
                        <x-exam.input wire:model="name" :disabled="!$isEditing" />
                    </x-exam.field>

                    <x-exam.field bare label="Email Address" required>
                        <x-exam.input type="email" wire:model="email" :disabled="!$isEditing" />
                    </x-exam.field>

                    <x-exam.field bare label="Phone Number">
                        <x-exam.input type="tel" id="phone" wire:model="phone" :disabled="!$isEditing" />
                    </x-exam.field>

                    <x-exam.field bare label="Date of Birth">
                        <x-exam.input type="date" wire:model="date_of_birth" :disabled="!$isEditing" />
                    </x-exam.field>

                    <x-exam.field bare label="Address" class="sm:col-span-2">
                        <x-exam.input wire:model="address" :disabled="!$isEditing" />
                    </x-exam.field>

                    <x-exam.field bare label="Bio" class="sm:col-span-2">
                        <textarea wire:model="bio" rows="3" @if(!$isEditing) disabled @endif
                        class="w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-60"></textarea>
                        @error('bio') <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p> @enderror
                    </x-exam.field>

                    <x-exam.field bare label="Emergency Contact Name">
                        <x-exam.input wire:model="emergency_contact_name" :disabled="!$isEditing" />
                    </x-exam.field>

                    <x-exam.field bare label="Emergency Contact Phone">
                        <x-exam.input type="tel" wire:model="emergency_contact_phone" :disabled="!$isEditing" />
                    </x-exam.field>
                </div>

                @if($isEditing)
                    <div class="flex justify-end gap-2 border-t border-line bg-surface-2 px-5 py-4">
                        <x-exam.button type="button" variant="ghost" wire:click="cancelEdit">Cancel</x-exam.button>
                        <x-exam.button type="submit" target="save" loading="Saving…">Save Changes</x-exam.button>
                    </div>
                @endif
            </form>
        </x-exam.card>

        {{-- Right rail --}}
        <div class="space-y-6">

            {{-- Profile picture --}}
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PROFILE PICTURE</p>
                </x-slot:header>
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        @if($currentAvatar)
                            <img src="{{ $currentAvatar }}" alt="" class="h-24 w-24 shrink-0 rounded-md object-cover ring-2 ring-line">
                        @else
                            <div class="grid h-24 w-24 shrink-0 place-items-center rounded-md border border-dashed border-line-strong bg-surface-2">
                                <svg class="h-8 w-8 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                        @endif

                        @if($isEditing)
                            <div class="min-w-0 flex-1">
                                {{-- VERIFY: wire:model="avatar" + removeAvatar handler --}}
                                <input type="file" id="avatar-upload" wire:model="avatar" accept="image/*" class="peer sr-only">
                                <label for="avatar-upload"
                                       class="group flex cursor-pointer items-center gap-2.5 rounded border-[1.5px] border-dashed border-line-strong px-3.5 py-3 text-[13px] text-ink-soft transition hover:border-primary hover:bg-surface-2 peer-focus-visible:border-primary peer-focus-visible:ring-[3px] peer-focus-visible:ring-primary/20">
                                    <svg class="h-[18px] w-[18px] text-muted transition group-hover:-translate-y-0.5 group-hover:text-primary motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    <span><b class="font-semibold text-ink">Choose new photo</b> — JPG or PNG</span>
                                </label>
                                @error('avatar') <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p> @enderror

                                @if($currentAvatar)
                                    <div class="mt-3">
                                        <x-exam.link-button tone="danger" wire:click="removeAvatar">REMOVE PHOTO</x-exam.link-button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </x-exam.card>

            {{-- Student group --}}
            @if($studentGroup)
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">STUDENT GROUP</p>
                    </x-slot:header>
                    <div class="space-y-2.5 p-5 font-mono text-[11px] tracking-wide text-muted">
                        <p><span class="text-ink-soft">GROUP:</span> {{ $studentGroup->name }}</p>
                        @if($studentGroup->description)
                            <p class="leading-relaxed"><span class="text-ink-soft">DESCRIPTION:</span> {{ $studentGroup->description }}</p>
                        @endif
                        @if($studentGroup->teacher)
                            <p><span class="text-ink-soft">TEACHER:</span> {{ $studentGroup->teacher->user->name }}</p>
                        @endif
                    </div>
                </x-exam.card>
            @endif

            {{-- Recent activity --}}
            @if($recentActivity && $recentActivity->count() > 0)
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">RECENT ACTIVITY</p>
                    </x-slot:header>
                    <ul class="divide-y divide-line">
                        @foreach($recentActivity as $activity)
                            <li class="flex items-center justify-between gap-3 px-5 py-3 transition hover:bg-surface-2/60">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-ink">{{ $activity->title ?? "Assessment #{$activity->id}" }}</p>
                                    <p class="mt-0.5 font-mono text-[10.5px] tracking-wide text-muted">{{ strtoupper($activity->created_at->diffForHumans()) }}</p>
                                </div>
                                @if($activity->score !== null)
                                    @php
                                    $sTone = $activity->score >= 80 ? 'ok' : ($activity->score >= 60 ? 'primary' : ($activity->score >= 40 ? 'warn' : 'danger'))
                                    @endphp
                                    <x-exam.badge :tone="$sTone">{{ $activity->score }}%</x-exam.badge>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-exam.card>
            @endif

        </div>
    </div>
</x-exam.page>
