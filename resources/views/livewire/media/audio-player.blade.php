<div>
    <div
        class="bg-white dark:bg-gray-900 rounded-lg shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700"
        x-data="audioPlayerData(@entangle('currentTime'), @entangle('duration'), @entangle('isPlaying'), @entangle('currentChapter'))"
        x-init="initPlayer('{{ $playerId }}', @js($this->getCurrentSource()))"
    >
        <!-- Compact Audio Player -->
        <div class="p-6">
            <div class="flex items-center gap-6">
                <!-- Album Art / Icon -->
                <div class="flex-shrink-0 w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                    </svg>
                </div>

                <!-- Player Info & Controls -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate mb-1">{{ $this->getTitle() }}</h3>
                    @if($this->isChapterBased())
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Chapter {{ $currentChapter + 1 }} of {{ count($this->getChapters()) }}
                        </p>
                    @endif

                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="relative group cursor-pointer" x-on:click="seekToPosition($event)">
                            <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-1.5 group-hover:h-2 transition-all">
                                <div
                                    class="bg-blue-600 h-full rounded-full relative"
                                    :style="`width: ${duration > 0 ? (currentTime / duration) * 100 : 0}%`"
                                >
                                    <div class="absolute right-0 top-1/2 w-3 h-3 bg-blue-600 rounded-full transform -translate-y-1/2 translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-600 dark:text-gray-400 font-mono" x-text="formatTime(currentTime)">0:00</span>
                            <span class="text-xs text-gray-600 dark:text-gray-400 font-mono" x-text="formatTime(duration)">0:00</span>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            @if($this->isChapterBased())
                                <button
                                    wire:click="playPreviousChapter"
                                    @disabled(!$this->hasPreviousChapter())
                                    class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"/>
                                    </svg>
                                </button>
                            @endif

                            <button
                                x-on:click="togglePlay()"
                                class="p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-md hover:shadow-lg transition-all"
                            >
                                <svg x-show="!isPlaying" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 5v10l8-5-8-5z"/>
                                </svg>
                                <svg x-show="isPlaying" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            @if($this->isChapterBased())
                                <button
                                    wire:click="playNextChapter"
                                    @disabled(!$this->hasNextChapter())
                                    class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4.555 5.168A1 1 0 003 6v8a1 1 0 001.555.832L10 11.202V14a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4A1 1 0 0010 6v2.798l-5.445-3.63z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <select
                                wire:model.live="playbackRate"
                                wire:change="changePlaybackRate($event.target.value)"
                                class="text-xs bg-gray-100 dark:bg-gray-800 border-0 rounded-lg px-2 py-1 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500"
                            >
                                @foreach($this->getPlaybackRateOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Audio Element -->
        <audio
            id="{{ $playerId }}"
            class="w-full"
            controls
            preload="metadata"
        >
            @if($this->getCurrentSource())
                <source src="{{ $this->getCurrentSource()['url'] }}" type="{{ $this->getCurrentSource()['type'] }}">
            @endif
        </audio>
    </div>

    <!-- Chapters List -->
    @if($this->isChapterBased())
        <div class="mt-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Chapters</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($this->getChapters() as $index => $chapter)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ $currentChapter === $index ? 'bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500' : '' }}" wire:key="audio-chapter-{{ $index }}">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-sm font-medium {{ $currentChapter === $index ? 'bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $chapter['title'] }}</h4>
                                    @if(!empty($chapter['description']))
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate">{{ $chapter['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $chapterDuration = $chapterDurations[$index] ?? $chapter['duration'] ?? 0;
                                @endphp
                                <div class="text-sm font-mono" 
                                     :class="currentChapter === {{ $index }} && isPlaying ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-500 dark:text-gray-400'">
                                    <template x-if="currentChapter === {{ $index }} && isPlaying">
                                        <span>
                                            <span x-text="formatTime(currentTime)"></span>
                                            <span class="text-gray-400 dark:text-gray-500">/</span>
                                            <span>{{ $this->formatTime($chapterDuration) }}</span>
                                        </span>
                                    </template>
                                    <template x-if="!(currentChapter === {{ $index }} && isPlaying)">
                                        <span>{{ $this->formatTime($chapterDuration) }}</span>
                                    </template>
                                </div>
                                @if(in_array($index, $completedChapters))
                                    <span class="text-green-500" title="Completed">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif
                                
                                <!-- Chapter Controls -->
                                <div class="flex items-center gap-1">
                                    <button 
                                        wire:click="jumpToChapter({{ $index }})" 
                                        class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                                        :class="currentChapter === {{ $index }} && isPlaying ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300'"
                                        :title="currentChapter === {{ $index }} && isPlaying ? 'Pause' : 'Play'">
                                        <template x-if="currentChapter === {{ $index }} && isPlaying">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </template>
                                        <template x-if="!(currentChapter === {{ $index }} && isPlaying)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </template>
                                    </button>
                                    
                                    <template x-if="currentChapter === {{ $index }}">
                                        <button 
                                            x-on:click="player.pause(); player.currentTime = 0;"
                                            class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                                            title="Stop">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <script>
        function audioPlayerData(currentTime, duration, isPlaying, currentChapter) {
            return {
                player: null,
                currentTime,
                duration,
                isPlaying,
                currentChapter,

                initPlayer(playerId, source) {
                    if (!source || !source.url) return;
                    
                    this.player = document.getElementById(playerId);
                    this.setupEventListeners();
                },

                setupEventListeners() {
                    this.player.addEventListener('timeupdate', () => {
                        this.currentTime = this.player.currentTime;
                        this.duration = this.player.duration || 0;
                        this.$wire.call('updateTime', this.currentTime, this.duration);
                    });

                    this.player.addEventListener('play', () => {
                        this.isPlaying = true;
                        this.$wire.call('setPlaying');
                    });

                    this.player.addEventListener('pause', () => {
                        this.isPlaying = false;
                        this.$wire.call('setPaused');
                    });

                    this.player.addEventListener('ended', () => {
                        this.isPlaying = false;
                        this.$wire.call('handleEnded');
                    });

                    this.player.addEventListener('volumechange', () => {
                        this.$wire.call('updateVolume', this.player.volume);
                    });

                    this.player.addEventListener('loadedmetadata', () => {
                        this.duration = this.player.duration || 0;
                    });
                },

                togglePlay() {
                    if (!this.player) return;
                    
                    if (this.isPlaying) {
                        this.player.pause();
                    } else {
                        this.player.play().catch(e => console.warn('Play failed:', e));
                    }
                },

                seekToPosition(event) {
                    if (!this.player || this.duration <= 0) return;
                    
                    const rect = event.currentTarget.getBoundingClientRect();
                    const percent = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
                    const time = percent * this.duration;
                    this.player.currentTime = time;
                },

                formatTime(seconds) {
                    if (!seconds || isNaN(seconds)) return '0:00';
                    
                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const secs = Math.floor(seconds % 60);

                    if (hours > 0) {
                        return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    }
                    return `${minutes}:${secs.toString().padStart(2, '0')}`;
                }
            }
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('setPlaybackRate', (event) => {
                const player = document.getElementById(event.playerId);
                if (player) player.playbackRate = event.rate;
            });

            Livewire.on('loadNewSource', (event) => {
                const player = document.getElementById(event.playerId);
                if (player && event.source) {
                    player.pause();
                    player.src = event.source.url;
                    player.load();
                    player.currentTime = 0;
                    if (event.autoplay !== false) {
                        player.play().catch(e => console.warn('Autoplay failed:', e));
                    }
                }
            });
            
            Livewire.on('pausePlayer', (event) => {
                const player = document.getElementById(event.playerId);
                if (player && !player.paused) {
                    player.pause();
                }
            });
        });
    </script>
</div>
