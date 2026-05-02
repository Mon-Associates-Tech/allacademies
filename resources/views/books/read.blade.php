<x-layouts.app :show-title-area="false" :full-width="true">
    <section
        class="relative h-[calc(100vh-6rem)]"
        x-data="bookPdfViewer({
            bookId: {{ $book->id }},
            title: @js($book->title),
            streamUrl: @js($streamUrl),
            progressUrl: @js(route('books.progress.update')),
            initialAnnotations: @js($initialAnnotations),
        })"
        x-init="init()"
    >
        <header class="absolute inset-x-0 top-0 z-30 h-14 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-b border-gray-200 dark:border-gray-700 px-3 md:px-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('books.show', $book) }}" class="px-2 py-1.5 text-sm rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200">Back</a>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate max-w-[15rem] md:max-w-[24rem]" title="{{ $book->title }}">{{ $book->title }}</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="previousPage()" :disabled="currentPage <= 1" class="px-2 py-1.5 text-xs rounded border border-gray-200 dark:border-gray-700 disabled:opacity-40 text-gray-700 dark:text-gray-200">Prev</button>
                <div class="text-xs text-gray-700 dark:text-gray-300 min-w-[4.5rem] text-center">
                    <span x-text="currentPage"></span>/<span x-text="totalPages || '-'"></span>
                </div>
                <button type="button" @click="nextPage()" :disabled="totalPages === 0 || currentPage >= totalPages" class="px-2 py-1.5 text-xs rounded border border-gray-200 dark:border-gray-700 disabled:opacity-40 text-gray-700 dark:text-gray-200">Next</button>
                <button type="button" @click="zoomOut()" class="px-2 py-1.5 text-xs rounded border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200">-</button>
                <span class="text-xs text-gray-700 dark:text-gray-300 min-w-[3rem] text-center" x-text="`${Math.round(zoomLevel * 100)}%`"></span>
                <button type="button" @click="zoomIn()" class="px-2 py-1.5 text-xs rounded border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200">+</button>
                <button type="button" @click="toggleAnnotationMode()" :class="annotationMode ? 'bg-amber-500 text-white border-amber-500' : 'text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700'" class="px-2 py-1.5 text-xs rounded border">
                    Annotate
                </button>
                @if($canDownload && $signedDownloadUrl)
                    <a href="{{ $signedDownloadUrl }}" class="px-2 py-1.5 text-xs rounded bg-blue-600 text-white">Download</a>
                @endif
            </div>
        </header>

        <div class="absolute inset-0 pt-14 bg-gray-200 dark:bg-gray-950 overflow-auto" wire:ignore>
            <div class="min-h-full flex items-start justify-center px-2 md:px-8 py-4">
                <div class="relative shadow-2xl bg-white" x-ref="pageWrapper">
                    <canvas x-ref="pdfCanvas" class="block"></canvas>

                    <div
                        class="absolute inset-0"
                        :class="annotationMode ? 'cursor-crosshair' : 'cursor-default'"
                        @mousedown.prevent="startDraw($event)"
                        @mousemove.prevent="moveDraw($event)"
                        @mouseup.prevent="endDraw($event)"
                        @mouseleave.prevent="cancelDraw()"
                        @touchstart.prevent="startDraw($event)"
                        @touchmove.prevent="moveDraw($event)"
                        @touchend.prevent="endDraw($event)"
                    >
                        <template x-for="annotation in pageAnnotations()" :key="annotation.id">
                            <button
                                type="button"
                                class="absolute border-2 rounded-sm"
                                :class="annotation.resolved_at ? 'border-emerald-500 bg-emerald-300/20' : 'border-amber-500 bg-amber-300/20'"
                                :style="annotationStyle(annotation)"
                                @click.stop="openAnnotation(annotation)"
                                :title="annotation.comments_count ? `${annotation.comments_count} comments` : 'Open discussion'"
                            ></button>
                        </template>

                        <div
                            x-show="annotationMode && tempBounds"
                            x-cloak
                            class="absolute border-2 border-dashed border-blue-500 bg-blue-300/20"
                            :style="tempBoundsStyle()"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <aside
            x-cloak
            x-show="showCommentsPanel"
            class="absolute top-14 right-0 bottom-0 z-40 w-full max-w-md"
        >
            <div class="h-10 bg-white dark:bg-gray-900 border-l border-b border-gray-200 dark:border-gray-700 px-3 flex items-center justify-between">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Comments</p>
                <button type="button" @click="showCommentsPanel = false" class="text-xs text-gray-500 dark:text-gray-400">Close</button>
            </div>
            <div class="h-[calc(100%-2.5rem)]">
                <livewire:books.annotation-thread-panel :book-id="$book->id" />
            </div>
        </aside>
    </section>
</x-layouts.app>
