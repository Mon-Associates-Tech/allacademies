<section>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white border border-gray-200 rounded-t-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Examination Heading</h3>
                            <p class="text-xs text-gray-600">Configure the examination title, duration, and
                                instructions</p>
                        </div>
                    </div>

                    <!-- Template Selector -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">Template:</span>
                        <div class="relative">
                            <select wire:model.live="template"
                                    id="heading_template"
                                    name="heading[template]"
                                    class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                                <option value="twig">Twig</option>
                                <option value="pug">Pug</option>
                                @isset($metadata['institution'])
                                    <option value="tera">Tera</option>
                                    <option value="jinja">Jinja</option>
                                @endisset
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div x-data="{
            preview: false,
            down: @entangle('down').live,
            up: @entangle('up').live,
            isFullscreen: false,
            toggleFullscreen() {
                this.isFullscreen = !this.isFullscreen;
            }
        }"
                 x-init="up = marked.parse(down)"
                 x-effect="up = marked.parse(down)"
                 :class="isFullscreen ? 'fixed inset-0 z-50 bg-white overflow-auto' : ''">

                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 bg-gray-50" :class="isFullscreen ? 'px-6 py-4' : 'px-6 py-3'">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-1">
                            <button x-on:click="preview = false"
                                    type="button"
                                    :class="!preview ? 'bg-white text-purple-700 border-purple-200 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                                    class="px-4 py-1 mr-2 text-sm font-medium rounded-lg border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <svg class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                                </svg>
                                Edit
                            </button>
                            <button x-on:click="preview = true"
                                    type="button"
                                    :class="preview ? 'bg-white text-purple-700 border-purple-200 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                                    class="px-4 py-1 text-sm font-medium rounded-lg border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <svg class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Preview
                            </button>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Template Info -->
                            <div
                                class="hidden sm:flex items-center space-x-2 text-xs text-gray-500 bg-white px-3 py-1.5 rounded-lg border">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                          clip-rule="evenodd"/>
                                </svg>
                                <span
                                    x-text="'Using ' + $wire.template.charAt(0).toUpperCase() + $wire.template.slice(1) + ' template'"></span>
                            </div>

                            <!-- Fullscreen Toggle -->
                            <button x-on:click="toggleFullscreen()"
                                    type="button"
                                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition-colors duration-200"
                                    :title="isFullscreen ? 'Exit fullscreen' : 'Enter fullscreen'">
                                <svg x-show="!isFullscreen" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
                                </svg>
                                <svg x-show="isFullscreen" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M15 9v-4.5M15 9h4.5M15 9l5.25-5.25M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 15v4.5M15 15h4.5m0 0l5.25 5.25"/>
                                </svg>
                            </button>

                            <!-- Close fullscreen -->
                            <button x-show="isFullscreen"
                                    x-on:click="isFullscreen = false"
                                    type="button"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    style="display: none;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <div x-show="!preview"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-2"
                     :class="isFullscreen ? 'p-8 h-full' : 'p-6'"
                     class="bg-gray-50">

                    <div class="space-y-6" :class="isFullscreen ? 'max-w-4xl mx-auto' : ''">
                        <!-- Title and Duration -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2">
                                <x-form.textarea wire:model.live="title"
                                                 name="heading[title]"
                                                 required
                                                 rows="1"
                                                 label="Examination Title"
                                                 required
                                                 style="resize: vertical"
                                                 placeholder="Enter the examination title..."
                                                 class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 resize-none transition-colors duration-200"></x-form.textarea>
                                @error('title')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="relative">
                                    <x-form.input wire:model.live="duration"
                                                  name="heading[duration]"
                                                  label="Duration (Minutes)"
                                                  type="text"
                                                  required
                                                  info-position="bottom"
                                                  info="The duration of the examination"
                                                  placeholder="e.g., 120"
                                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 pr-16 transition-colors duration-200"></x-form.input>

                                </div>
                                @error('duration')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="space-y-2">
                            <div class="">
                                <x-form.rich-editor wire:model.live="instructions"
                                                    class="rich-editor min-h-[200px]"
                                                    name="heading[instructions]"
                                                    label="Header Instructions"
                                                    wire:key="heading-instructions"
                                                    info="Add detailed instructions for the examination"
                                                    :has-label="false"/>
                            </div>
                            @error('instructions')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Template Help -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <svg class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-blue-900">Template Information</h4>
                                    <div class="text-sm text-blue-700 mt-1">
                                        <p>The selected template (<span class="font-medium"
                                                                        x-text="$wire.template"></span>) will be used to
                                            render the examination heading with your institution's information.</p>
                                        <p class="mt-1">Click "Preview" to see how the final output will look.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Mode -->
                <div x-show="preview"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-2"
                     style="display: none;"
                     :class="isFullscreen ? 'p-8 h-full' : 'p-6'"
                     class="bg-white">

                    <div :class="isFullscreen ? 'max-w-4xl mx-auto' : ''">
                        <!-- Preview Header -->
                        <div class="mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">Preview</h4>
                                    <p class="text-xs text-gray-600">This is how the examination heading will appear</p>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Content -->
                        <div class="prose max-w-none">
                            <div x-html="up"
                                 class="font-serif text-gray-800 leading-relaxed examination-preview"
                                 style="font-family: 'Times New Roman', serif;">
                            </div>
                        </div>

                        <!-- Preview Footer -->
                        <div class="mt-8 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Template: <span class="font-medium"
                                                      x-text="$wire.template.charAt(0).toUpperCase() + $wire.template.slice(1)"></span></span>
                                <span>Last updated: <span x-text="new Date().toLocaleString()"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields for form submission -->
                <textarea x-model="down" id="heading_down" name="heading[down]" class="hidden"></textarea>
                <textarea x-model="up" id="heading_up" name="heading[up]" class="hidden"></textarea>
            </div>
        </div>

        <!-- Error Messages -->
        @error('heading.up')
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <p class="text-sm text-red-700">{{ $message }}</p>
            </div>
        </div>
        @enderror
    </div>

    <style>
        .examination-preview h1,
        .examination-preview h2,
        .examination-preview h3 {
            @apply text-center font-bold text-gray-900 mb-4;
        }

        .examination-preview h1 {
            @apply text-2xl;
        }

        .examination-preview h2 {
            @apply text-xl;
        }

        .examination-preview h3 {
            @apply text-lg;
        }

        .examination-preview p {
            @apply text-center text-gray-800 mb-2;
        }

        .examination-preview strong {
            @apply font-semibold;
        }

        .examination-preview em {
            @apply italic;
        }
    </style>
</section>
