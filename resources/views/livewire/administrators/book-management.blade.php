<x-exam.page :maxWidth="'max-w-7xl'">
    <!-- Header -->
    <x-exam.header
        :title="'Book Management'"
        :sub="'Manage your library\'s book collection'"
        :eyebrow="'ADMINISTRATION'"
    />

    <!-- Action Bar -->
    <div class="mb-6 flex items-center justify-between gap-4">
        <a href="{{ route('books.index') }}"
           class="whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary hover:border-current hover:text-primary-hi focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition">
            Browse Books
        </a>
        <a href="{{ route('admin.books.create') }}"
           class="inline-flex items-center justify-center whitespace-nowrap rounded px-5 py-2.5 text-[13.5px] font-semibold tracking-wide transition hover:-translate-y-px focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent active:translate-y-0 bg-primary text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] hover:bg-primary-hi">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Book
        </a>
    </div>

    <!-- Notifications -->
    @if (session()->has('message'))
        <x-exam.flash type="success" :message="session('message')"/>
    @endif

    @if (session()->has('error'))
        <x-exam.flash type="error" :message="session('error')"/>
    @endif

    @if (session()->has('success'))
        <x-exam.flash type="success" :message="session('success')"/>
    @endif

    {{-- Add/Edit Form --}}
    @if($showForm)
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">
                            {{ $isEditing ? 'Edit Book' : 'Add New Book' }}
                        </h2>
                        <p class="text-sm text-muted mt-0.5">
                            {{ $isEditing ? 'Update book information and settings' : 'Fill in the details to add a new book to your library' }}
                        </p>
                    </div>
                    <x-exam.button variant="ghost" wire:click="hideForm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </x-exam.button>
                </div>
            </x-slot:header>

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}" class="p-6 space-y-8">
                {{-- Step 1: Basic Information --}}
                <div>
                    <x-exam.callout class="mb-4">
                        <div class="flex items-center gap-3">
                            <x-exam.badge tone="primary">1</x-exam.badge>
                            <div>
                                <h3 class="font-semibold text-ink">Basic Information</h3>
                                <p class="text-sm text-muted">Enter the fundamental details about the book</p>
                            </div>
                        </div>
                    </x-exam.callout>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                        <div class="lg:col-span-2">
                            <x-exam.field :label="'Book Title'" :required="true" tag="lg">
                                <x-exam.input type="text" wire:model="title"
                                              placeholder="Enter the complete book title"/>
                            </x-exam.field>
                        </div>

                        <x-exam.field :label="'URL Slug'" tag="sm">
                            <x-exam.input type="text" wire:model="slug" readonly/>
                        </x-exam.field>

                        <x-exam.field :label="'Author'" :required="true" tag="sm">
                            <x-exam.select wire:model="authorId">
                                <option value="">Choose an author</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->user?->name }}</option>
                                @endforeach
                            </x-exam.select>
                        </x-exam.field>

                        <x-exam.field :label="'Category'" :required="true" tag="sm">
                            <x-exam.select wire:model="bookCategoryId">
                                <option value="">Select a category</option>
                                @foreach($bookCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-exam.select>
                        </x-exam.field>

                        <x-exam.field :label="'Edition'" tag="sm">
                            <x-exam.input type="text" wire:model="edition"
                                          placeholder="e.g., 1st Edition, Revised Edition"/>
                        </x-exam.field>

                        <x-exam.field :label="'Publisher'" tag="sm">
                            <x-exam.input type="text" wire:model="publisher" placeholder="Publishing house name"/>
                        </x-exam.field>

                        <x-exam.field :label="'Number of Pages'" tag="sm">
                            <x-exam.input type="number" wire:model="pages" min="1" placeholder="Total pages"/>
                        </x-exam.field>

                        <x-exam.field :label="'Annual Subscription Fee (GHS)'" tag="sm">
                            <x-exam.input type="number" wire:model="annualSubscriptionFee" step="0.01" min="0"
                                          placeholder="0.00"/>
                        </x-exam.field>
                    </div>
                </div>

                {{-- Step 2: Book Formats --}}
                <div>
                    <x-exam.callout class="mb-4">
                        <div class="flex items-center gap-3">
                            <x-exam.badge tone="primary">2</x-exam.badge>
                            <div>
                                <h3 class="font-semibold text-ink">Available Formats</h3>
                                <p class="text-sm text-muted">Select the formats in which this book is available</p>
                            </div>
                        </div>
                    </x-exam.callout>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label
                            class="relative flex items-start p-4 bg-surface-2 border-2 border-line rounded-lg cursor-pointer hover:border-primary/50 transition-colors">
                            <input type="checkbox" wire:model="hasHardcopy"
                                   class="h-5 w-5 text-primary focus:ring-primary border-line rounded mt-0.5">
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-ok mr-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="font-medium text-ink">Physical Hardcopy</span>
                                </div>
                                <p class="text-sm text-muted mt-1">Traditional printed book available for borrowing</p>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start p-4 bg-surface-2 border-2 border-line rounded-lg cursor-pointer hover:border-primary/50 transition-colors">
                            <input type="checkbox" wire:model="hasSoftcopy"
                                   class="h-5 w-5 text-primary focus:ring-primary border-line rounded mt-0.5">
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-medium text-ink">Digital Softcopy</span>
                                </div>
                                <p class="text-sm text-muted mt-1">PDF version for online reading and downloads</p>
                            </div>
                        </label>
                    </div>
                    @error('hasHardcopy')
                    <p class="mt-2 text-sm text-accent flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"></path>
                        </svg>{{ $message }}</p> @enderror
                </div>

                {{-- Step 3: Media Files --}}
                <div>
                    <x-exam.callout class="mb-4">
                        <div class="flex items-center gap-3">
                            <x-exam.badge tone="primary">3</x-exam.badge>
                            <div>
                                <h3 class="font-semibold text-ink">Media & Files</h3>
                                <p class="text-sm text-muted">Upload cover image and PDF file</p>
                            </div>
                        </div>
                    </x-exam.callout>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Cover Image Upload --}}
                        <x-exam.field :label="'Cover Image'">
                            <x-exam.dropzone id="coverImage" model="coverImage"
                                             :current="$isEditing && $existingCover && !$coverImage ? Storage::url($existingCover) : null"
                                             :pending="(bool)$coverImage" tag="COVER IMAGE"/>
                        </x-exam.field>

                        {{-- PDF Upload --}}
                        <x-exam.field :label="'PDF File'">
                            <div class="mb-1">
                                <x-exam.badge tone="primary">Required for softcopy</x-exam.badge>
                            </div>
                            <x-exam.file-upload id="pdfFile" model="pdfFile" icon="pdf" accept=".pdf"
                                              :current="$isEditing && $existingPdf && !$pdfFile ? Storage::url($existingPdf) : null"
                                              :pending="(bool)$pdfFile"
                                              removeAction="removeExistingPdfFile" tag="PDF FILE"/>
                            @if ($isEditing && $existingPdf && !$pdfFile)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-muted mb-2">Current PDF:</p>
                                    <a href="{{ Storage::url($existingPdf) }}" target="_blank"
                                       class="whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary hover:border-current hover:text-primary-hi focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition inline-flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Current PDF
                                    </a>
                                </div>
                            @endif
                        </x-exam.field>
                    </div>
                </div>

                {{-- Step 4: Additional Details --}}
                <div>
                    <x-exam.callout class="mb-4">
                        <div class="flex items-center gap-3">
                            <x-exam.badge tone="primary">4</x-exam.badge>
                            <div>
                                <h3 class="font-semibold text-ink">Additional Details</h3>
                                <p class="text-sm text-muted">Extra information and subscription conditions</p>
                            </div>
                        </div>
                    </x-exam.callout>

                    <div class="space-y-4">
                        <x-exam.field :label="'Additional Information'">
                            <textarea wire:model="additionalInfo" rows="4"
                                      placeholder="Enter any additional information about the book, such as description, special notes, or summary..."
                                      class="w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20 resize-none"></textarea>
                            @error('additionalInfo') <p class="mt-2 font-mono text-[11.5px] text-accent">
                                ▲ {{ $message }}</p> @enderror
                        </x-exam.field>

                        <x-exam.field :label="'Subscription Conditions'">
                            <textarea wire:model="subscriptionConditions" rows="4"
                                      placeholder="Enter specific terms and conditions for subscribing to this book. Leave blank to use default library conditions..."
                                      class="w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20 resize-none"></textarea>
                            @error('subscriptionConditions') <p class="mt-2 font-mono text-[11.5px] text-accent">
                                ▲ {{ $message }}</p> @enderror
                        </x-exam.field>
                    </div>
                </div>
            </form>

            <x-slot:footer>
                <x-exam.savebar :hint="'Required fields are marked with *'">
                    <div class="flex items-center gap-3">
                        <x-exam.button variant="ghost" type="button" wire:click="hideForm">
                            Cancel
                        </x-exam.button>
                        <x-exam.button type="submit" variant="ok" target="{{ $isEditing ? 'update' : 'create' }}">
                            @if($isEditing)
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Update Book
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Book
                            @endif
                        </x-exam.button>
                    </div>
                </x-exam.savebar>
            </x-slot:footer>
        </x-exam.card>
    @endif

    {{-- Books List --}}
    <x-exam.card :padded="false">
        {{-- Filters --}}
        <div class="px-6 py-4 border-b border-line">
            <div class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <x-exam.input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search books..."
                                  class="pl-10"/>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <x-exam.select wire:model="filterCategory">
                        <option value="">All Categories</option>
                        @foreach($bookCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </x-exam.select>

                    <x-exam.select wire:model="filterAuthor">
                        <option value="">All Authors</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}">{{ $author->user?->name }}</option>
                        @endforeach
                    </x-exam.select>

                    <x-exam.select wire:model="filterFormat">
                        <option value="">All Formats</option>
                        <option value="hardcopy">Hardcopy Only</option>
                        <option value="softcopy">Softcopy Only</option>
                        <option value="both">Both Formats</option>
                    </x-exam.select>

                    <x-exam.button variant="ghost" wire:click="resetFilters">
                        Reset
                    </x-exam.button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <x-exam.table>
            <x-slot:head>
                <tr class="border-b border-line bg-surface-2">
                    <th class="w-8 px-6 py-3 text-center">
                        <input type="checkbox" wire:model="selectAll"
                               class="h-4 w-4 text-primary focus:ring-primary border-line rounded">
                    </th>
                    <th class="w-80 px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:bg-surface-3"
                        wire:click="sortBy('title')">
                        <div class="flex items-center space-x-1">
                            <span>Title</span>
                            @if($sortBy === 'title')
                                <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th class="w-24 px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                        Status
                    </th>
                    <th class="w-32 px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </x-slot:head>

            @forelse($books as $book)
                <tr class="hover:bg-surface-2 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <input type="checkbox" wire:model="selectedBooks" value="{{ $book->id }}"
                               class="h-4 w-4 text-primary focus:ring-primary border-line rounded">
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{route('books.show', $book)}}" class="group">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">

                                    @if($book->cover_image)
                                        <img src="{{$book->cover_image }}" alt="{{ $book->title }}"
                                             class="h-16 w-12 object-cover overflow-hidden rounded shadow-sm border border-line">
                                    @else
                                        <div
                                            class="h-16 w-12 bg-surface-2 rounded flex items-center justify-center border border-line">
                                            <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div
                                        class="text-sm font-semibold text-ink leading-5 group-hover:text-primary transition-colors">{{ $book->title }}</div>
                                    <div class="text-xs text-muted mt-0.5">
                                        <span class="text-sm font-medium text-ink">{{ $book->author_name }}</span>
                                        @if($book->edition)
                                            · Edition {{ $book->edition }}
                                        @endif
                                        @if($book->publisher)
                                            · {{ $book->publisher }}
                                        @endif
                                        @if($book->pages)
                                            · {{ $book->pages }} pages
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        @if($book->has_hardcopy)
                                            <x-exam.badge tone="ok">Hardcopy</x-exam.badge>
                                        @endif
                                        @if($book->has_softcopy)
                                            <x-exam.badge tone="primary">Softcopy</x-exam.badge>
                                        @endif
                                        @if($book->annual_subscription_fee > 0)
                                            <span
                                                class="text-xs font-medium text-warn">₵{{ number_format($book->annual_subscription_fee, 2) }}</span>
                                        @else
                                            <span class="text-xs font-medium text-ok">Free</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @php
                            $statusEnum = App\Enums\PublishingStatus::fromLegacy($book->status);
                            $isPublished = $statusEnum === App\Enums\PublishingStatus::PUBLISHED;
                        @endphp

                        <div class="flex flex-col items-center space-y-1">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="!!{{ $isPublished ? 'true' : 'false' }}"
                                       wire:click="toggleBookStatus({{ $book->id }})"
                                       class="sr-only peer">
                                <div
                                    class="relative w-10 h-6 bg-line peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-muted after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ok hover:peer-checked:bg-ok/90"></div>
                            </label>
                            <span class="text-xs font-medium {{ $isPublished ? 'text-ok' : 'text-warn' }}">
                                {{ $statusEnum->getLabel() }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-1">
                            <a href="{{ route('admin.books.edit', $book->id) }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded px-5 py-2.5 text-[13.5px] font-semibold tracking-wide transition hover:-translate-y-px focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent active:translate-y-0 border border-line-strong bg-transparent text-ink-soft hover:bg-surface-2 hover:text-ink"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @if($book->has_softcopy && $book->pdf_file)
                                <a href="{{ Storage::url($book->pdf_file) }}" target="_blank"
                                   class="inline-flex items-center justify-center whitespace-nowrap rounded px-5 py-2.5 text-[13.5px] font-semibold tracking-wide transition hover:-translate-y-px focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent active:translate-y-0 border border-line-strong bg-transparent text-ink-soft hover:bg-surface-2 hover:text-ink"
                                   title="View PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            @endif
                            <x-exam.button variant="danger"
                                           wire:click="delete({{ $book->id }})"
                                           onclick="return confirm('Are you sure you want to delete this book?')"
                                           title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </x-exam.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="py-12">
                            <x-exam.empty :title="'No books found'"
                                          :description="'Try adjusting your search or filter criteria'"/>
                        </div>
                    </td>
                </tr>
            @endforelse

            @if($books->hasPages())
                <x-slot:footer>
                    {{ $books->links() }}
                </x-slot:footer>
            @endif
        </x-exam.table>

        {{-- Bulk Actions --}}
        @if(count($selectedBooks) > 0)
            <div class="border-t border-line bg-primary/5 px-6 py-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-ink">{{ count($selectedBooks) }} book(s) selected</span>
                    <x-exam.button variant="danger"
                                   wire:click="bulkDelete"
                                   onclick="return confirm('Are you sure you want to delete selected books?')">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Selected
                    </x-exam.button>
                </div>
            </div>
        @endif
    </x-exam.card>
</x-exam.page>
