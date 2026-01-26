<div>
    <div
        class="bg-white dark:bg-gray-900 rounded-lg shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700"
        x-data="videoPlayerData(@entangle('currentTime'), @entangle('duration'), @entangle('isPlaying'), @entangle('currentChapter'))"
        x-init="initPlayer('{{ $playerId }}', @js($this->getCurrentSource()))"
        @tab-switched.window="if ($event.detail.playerId !== '{{ $playerId }}') pausePlayer()"
    >
        <!-- Video Player Container - Fixed Aspect Ratio -->
        <div class="relative bg-black aspect-video">
            <video
                id="{{ $playerId }}"
                class="video-js vjs-theme-custom w-full h-full"
                controls
                preload="metadata"
                data-setup='{}'
                poster="{{ $this->getThumbnailUrl() }}"
            >
                @if($this->getCurrentSource())
                    <source src="{{ $this->getCurrentSource()['url'] }}" type="{{ $this->getCurrentSource()['type'] }}">
                @endif

                @if($this->getCaptionsUrl())
                    <track
                        kind="captions"
                        src="{{ $this->getCaptionsUrl() }}"
                        srclang="en"
                        label="English"
                        {{ $showCaptions ? 'default' : '' }}
                    >
                @endif

                <p class="vjs-no-js text-white p-4">
                    To view this video please enable JavaScript, and consider upgrading to a web browser that
                    <a href="https://videojs.com/html5-video-support/" target="_blank" class="text-blue-400 underline">
                        supports HTML5 video
                    </a>.
                </p>
            </video>

            <!-- Loading Overlay -->
            <div
                x-show="!playerReady"
                x-transition:enter="transition-opacity duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center"
            >
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
                    <p class="text-white text-sm">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Media Information -->
        <div class="mt-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $this->getTitle() }}</h1>
                    @if($this->getDescription())
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $this->getDescription() }}</p>
                    @endif
                    @if($this->isChapterBased())
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            {{ count($this->getChapters()) }} chapters
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chapters -->
        @if($this->isChapterBased())
            <div class="mt-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Chapters</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->getChapters() as $index => $chapter)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ $currentChapter === $index ? 'bg-blue-50 dark:bg-blue-900 border-r-4 border-blue-500' : '' }}" wire:key="video-chapter-{{ $index }}">
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
                                                x-on:click="if(player) { player.pause(); player.currentTime(0); }"
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

        <style>
            .vjs-theme-custom .vjs-control-bar {
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(8px);
            }

            .vjs-theme-custom .vjs-big-play-button {
                background: rgba(37, 99, 235, 0.9);
                border: none;
                border-radius: 50%;
                width: 72px;
                height: 72px;
                line-height: 72px;
                font-size: 20px;
            }

            .vjs-theme-custom .vjs-big-play-button:hover {
                background: rgba(37, 99, 235, 1);
            }

            .vjs-theme-custom .vjs-play-progress {
                background: #2563eb;
            }

            .vjs-theme-custom .vjs-volume-level {
                background: #2563eb;
            }
        </style>

        <script>
            function videoPlayerData(currentTime, duration, isPlaying, currentChapter) {
                return {
                    player: null,
                    playerReady: false,
                    playerId: '',
                    currentTime,
                    duration,
                    isPlaying,
                    currentChapter,

                    initPlayer(playerId, source) {
                        this.playerId = playerId;
                        if (!source || !source.url) return;

                        this.player = videojs(playerId, {
                            responsive: true,
                            fluid: true,
                            playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 2],
                            controls: true,
                            preload: 'metadata'
                        });

                        this.player.ready(() => {
                            this.setupEventListeners();
                            this.playerReady = true;
                        });
                    },

                    setupEventListeners() {
                        this.player.on('timeupdate', () => {
                            this.currentTime = this.player.currentTime();
                            this.duration = this.player.duration() || 0;
                            this.$wire.call('updateTime', this.currentTime, this.duration);
                        });

                        this.player.on('play', () => {
                            this.isPlaying = true;
                            this.$wire.call('setPlaying');
                            window.dispatchEvent(new CustomEvent('tab-switched', { detail: { playerId: this.playerId } }));
                        });

                        this.player.on('pause', () => {
                            this.isPlaying = false;
                            this.$wire.call('setPaused');
                        });

                        this.player.on('ended', () => {
                            this.isPlaying = false;
                            this.$wire.call('handleEnded');
                        });

                        this.player.on('volumechange', () => {
                            this.$wire.call('updateVolume', this.player.volume());
                        });

                        this.player.on('loadedmetadata', () => {
                            this.duration = this.player.duration() || 0;
                        });

                        this.player.on('error', (error) => {
                            console.error('Player error:', error);
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

                    pausePlayer() {
                        if (this.player && this.isPlaying) {
                            this.player.pause();
                        }
                    },

                    toggleFullscreen() {
                        if (!this.player) return;

                        if (this.player.isFullscreen()) {
                            this.player.exitFullscreen();
                        } else {
                            this.player.requestFullscreen();
                        }
                    },

                    seekToPosition(event) {
                        if (!this.player || this.duration <= 0) return;

                        const rect = event.currentTarget.getBoundingClientRect();
                        const percent = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
                        const time = percent * this.duration;
                        this.player.currentTime(time);
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
                    const player = videojs(event.playerId);
                    if (player) {
                        player.playbackRate(event.rate);
                    }
                });

                Livewire.on('toggleCaptions', (event) => {
                    const player = videojs(event.playerId);
                    if (player) {
                        const tracks = player.textTracks();
                        if (tracks.length > 0) {
                            tracks[0].mode = event.show ? 'showing' : 'disabled';
                        }
                    }
                });

                Livewire.on('seekTo', (event) => {
                    const player = videojs(event.playerId);
                    if (player) {
                        player.currentTime(event.time);
                        if (event.autoplay !== false) {
                            player.play().catch(e => console.warn('Seek play failed:', e));
                        }
                    }
                });

                Livewire.on('loadNewSource', (event) => {
                    const player = videojs(event.playerId);
                    if (player && event.source) {
                        player.src({ src: event.source.url, type: event.source.type });
                        player.load();
                        if (event.autoplay !== false) {
                            player.play().catch(e => console.warn('Autoplay failed:', e));
                        }
                    }
                });

                Livewire.on('pausePlayer', (event) => {
                    const player = videojs(event.playerId);
                    if (player && !player.paused()) {
                        player.pause();
                    }
                });
            });
        </script>
    </div>
</div>
