<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Financial Aid & Payment Codes</h2>
        <button wire:click="create" class="px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-violet-700 transition-colors">
            Create New Aid
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Search Input --}}
    <div class="mb-4 relative">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or code..." class="pl-4 w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-violet-500 focus:border-violet-500">
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Covered Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applies To</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Beneficiaries</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
            @forelse($aids as $aid)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $aid->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 font-mono">{{ $aid->code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">GHS {{ number_format($aid->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                        @if($aid->schoolPaymentStructure)
                            <span class="block text-violet-600 dark:text-violet-400">{{ $aid->schoolPaymentStructure->name }}</span>
                            <span class="text-xs text-gray-400">({{ ucfirst(str_replace('_', ' ', $aid->schoolPaymentStructure->payment_type)) }})</span>
                        @else
                            <span class="text-gray-400">General Payment</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $aid->beneficiaries_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $aid->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ ucfirst($aid->status) }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="openBeneficiaries({{ $aid->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 mr-3">Manage</button>
                        <button wire:click="edit({{ $aid->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Edit</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No records found.</td></tr>
            @endforelse
            </tbody>
        </table>
        @if($aids->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $aids->links() }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                    @if($manageBeneficiariesMode)
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4" x-data="{ tab: 'manual' }">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Manage Students: <span class="text-violet-600">{{ $currentAid->name }}</span></h3>

                            {{-- Tabs Navigation --}}
                            <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
                                <button @click="tab = 'manual'" :class="{ 'border-violet-500 text-violet-600': tab === 'manual', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'manual' }" class="flex-1 py-2 px-4 text-center border-b-2 font-medium text-sm">
                                    Manual Input
                                </button>
                                <button @click="tab = 'csv'" :class="{ 'border-violet-500 text-violet-600': tab === 'csv', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'csv' }" class="flex-1 py-2 px-4 text-center border-b-2 font-medium text-sm">
                                    CSV Import
                                </button>
                                <button @click="tab = 'group'" :class="{ 'border-violet-500 text-violet-600': tab === 'group', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'group' }" class="flex-1 py-2 px-4 text-center border-b-2 font-medium text-sm">
                                    Student Group
                                </button>
                            </div>

                            {{-- Tab 1: Manual Input --}}
                            <div x-show="tab === 'manual'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enter IDs or Emails</label>
                                <textarea wire:model="beneficiaryInput" class="w-full border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-violet-500 focus:border-violet-500" rows="3" placeholder="STU001, student@example.com..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">Comma or newline separated.</p>
                                <button wire:click="addBeneficiariesFromInput" class="mt-3 w-full bg-violet-600 hover:bg-violet-700 text-white py-2 rounded-md text-sm font-medium transition-colors">Add Students</button>
                            </div>

                            {{-- Tab 2: CSV Import --}}
                            <div x-show="tab === 'csv'" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload CSV File</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-violet-600 hover:text-violet-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-violet-500">
                                                <span>Upload a file</span>
                                                <input id="file-upload" wire:model="beneficiaryFile" type="file" class="sr-only" accept=".csv,.txt">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">CSV with IDs or Emails in the first column.</p>
                                    </div>
                                </div>
                                @error('beneficiaryFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                                <button wire:click="addBeneficiariesFromFile" class="mt-3 w-full bg-violet-600 hover:bg-violet-700 text-white py-2 rounded-md text-sm font-medium transition-colors"
                                        @if(!$beneficiaryFile) disabled class="opacity-50 cursor-not-allowed" @endif>
                                    Process File
                                </button>
                            </div>

                            {{-- Tab 3: Student Group --}}
                            <div x-show="tab === 'group'" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Student Group</label>
                                <select wire:model="selectedGroupId" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">-- Select Group --</option>
                                    @foreach($availableStudentGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->students_count ?? 'Unknown' }} students)</option>
                                    @endforeach
                                </select>
                                @error('selectedGroupId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                                <button wire:click="addBeneficiariesFromGroup" class="mt-3 w-full bg-violet-600 hover:bg-violet-700 text-white py-2 rounded-md text-sm font-medium transition-colors"
                                        @if(!$selectedGroupId) disabled class="opacity-50 cursor-not-allowed" @endif>
                                    Import Group
                                </button>
                            </div>

                            {{-- List of Beneficiaries --}}
                            <div class="mt-6">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300">Current Beneficiaries</h4>
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 py-0.5 px-2 rounded-full text-xs">{{ $currentAid->beneficiaries->count() }}</span>
                                </div>
                                <div class="max-h-40 overflow-y-auto border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                    @forelse($currentAid->beneficiaries as $student)
                                        <div class="flex justify-between items-center px-3 py-2 border-b dark:border-gray-700 last:border-0">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium dark:text-gray-200">{{ $student->user->name ?? 'Unknown' }}</span>
                                                <span class="text-xs text-gray-500">{{ $student->student_id }}</span>
                                            </div>
                                            <button wire:click="removeBeneficiary({{ $student->id }})" class="text-red-500 hover:text-red-700 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="px-3 py-4 text-center text-sm text-gray-500">No beneficiaries yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                            <button wire:click="closeModal" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">Close</button>
                        </div>
                    @else
                        {{-- Edit/Create Form ... (kept same) --}}
                        <form wire:submit.prevent="store">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ $aidId ? 'Edit' : 'Create' }} Financial Aid</h3>
                                {{-- ... form fields same as before ... --}}
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                        <input wire:model="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    {{-- ... other fields ... --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code</label>
                                            <input wire:model="code" type="text" placeholder="Auto-gen" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Covered Amount</label>
                                            <input wire:model="amount" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="border-t border-b py-3 dark:border-gray-700">
                                        <label class="block text-sm text-gray-700 dark:text-gray-300">Applies to Payment Structure</label>
                                        <select wire:model="school_payment_structure_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                            <option value="">-- Select Payment Structure --</option>
                                            @foreach($availableStructures as $struct)
                                                <option value="{{ $struct['id'] }}">{{ $struct['label'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('school_payment_structure_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                        <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                                <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">Cancel</button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    @endif
</div>
