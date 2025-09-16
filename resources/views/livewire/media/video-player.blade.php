<div class="">
    <section>
        <div
            class="bg-white dark:bg-gray-900 rounded-lg shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700"
            x-data="videoPlayerData(@entangle('currentTime'), @entangle('duration'), @entangle('isPlaying'))"
            x-init="initPlayer('{{ $playerId }}', @js($this->getCurrentSource()))"
        >
            <!-- Video/Audio Player Container -->
            <div class="relative bg-black group">
                @if(isset($mediaData['type']) &&  $mediaData['type'] === 'video')
                    <video
                        id="{{ $playerId }}"
                        class="video-js vjs-theme-custom w-full h-96 object-cover"
                        controls
                        preload="metadata"
                        data-setup="{}"
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
                @else
                    <audio
                        id="{{ $playerId }}"
                        class="video-js vjs-theme-custom w-full"
                        controls
                        preload="metadata"
                        data-setup="{}"
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
                            To listen to this audio please enable JavaScript, and consider upgrading to a web browser that
                            <a href="https://videojs.com/html5-video-support/" target="_blank" class="text-blue-400 underline">
                                supports HTML5 audio
                            </a>.
                        </p>
                    </audio>

                    <!-- Audio Visual Display -->
                    <div class="flex items-center justify-center h-64 bg-gradient-to-br from-slate-800 to-slate-900">
                        <div class="text-center px-8">
                            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-slate-700 flex items-center justify-center ring-2 ring-slate-600">
                                <svg class="w-10 h-10 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                                </svg>
                            </div>
                            <h3 class="text-white text-xl font-semibold mb-2">{{ $this->getTitle() }}</h3>
                            <p class="text-slate-400">Audio Content</p>
                        </div>
                    </div>
                @endif

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

            <!-- Custom Progress Bar -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="relative group cursor-pointer" x-on:click="seekToPosition($event)">
                    <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-1 group-hover:h-2 transition-all duration-200">
                        <div
                            class="bg-blue-600 h-full rounded-full relative transition-all duration-300"
                            :style="`width: ${duration > 0 ? (currentTime / duration) * 100 : 0}%`"
                        >
                            <div class="absolute right-0 top-1/2 w-3 h-3 bg-blue-600 rounded-full transform -translate-y-1/2 translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg"></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-mono" x-text="formatTime(currentTime)">0:00</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-mono" x-text="formatTime(duration)">0:00</span>
                </div>
            </div>

            <!-- Controls -->
            <div class="px-6 py-4 bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <!-- Primary Controls -->
                    <div class="flex items-center space-x-3">
                        @if($this->isChapterBased())
                            <button
                                wire:click="playPreviousChapter"
                                @disabled(!$this->hasPreviousChapter())
                                class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg disabled:opacity-50 disabled:hover:bg-transparent disabled:cursor-not-allowed transition-colors duration-200"
                                title="Previous Chapter"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"/>
                                </svg>
                            </button>
                        @endif

                        <button
                            x-on:click="togglePlay()"
                            class="p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
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
                                class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg disabled:opacity-50 disabled:hover:bg-transparent disabled:cursor-not-allowed transition-colors duration-200"
                                title="Next Chapter"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4.555 5.168A1 1 0 003 6v8a1 1 0 001.555.832L10 11.202V14a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4A1 1 0 0010 6v2.798l-5.445-3.63z"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    <!-- Secondary Controls -->
                    <div class="flex items-center space-x-4">
                        @if($this->isChapterBased())
                            <div class="hidden sm:block">
                            <span class="text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                                Chapter {{ $currentChapter + 1 }} of {{ count($this->getChapters()) }}
                            </span>
                            </div>
                        @endif

                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600 dark:text-gray-400 hidden sm:inline">Speed</label>
                            <select
                                wire:model.live="playbackRate"
                                wire:change="changePlaybackRate($event.target.value)"
                                class="text-sm bg-gray-100 dark:bg-gray-800 border-0 rounded-lg px-3 py-1 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500"
                            >
                                @foreach($this->getPlaybackRateOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($this->getCaptionsUrl())
                            <button
                                wire:click="toggleCaptions"
                                class="text-sm font-medium px-3 py-1 rounded-lg border transition-colors duration-200 {{ $showCaptions ? 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700' : 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700' }}"
                            >
                                CC
                            </button>
                        @endif

                        @if(!empty($mediaData) && $mediaData['type'] === 'video')
                            <button
                                x-on:click="toggleFullscreen()"
                                class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200"
                                title="Fullscreen"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Information -->
        <div class="mt-6 grid lg:grid-cols-3 gap-6">
            <!-- Content Details -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            @if(!empty($mediaData) && $mediaData['type'] === 'video')
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $this->getTitle() }}</h1>
                        @if($this->getDescription())
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $this->getDescription() }}</p>
                        @endif
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $this->formatTime($duration) }}
                        </span>
                            <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2"/>
                            </svg>
                            {{ !empty($mediaData) ? ucfirst($mediaData['type']) : 'Media' }}
                        </span>
                            @if($this->isChapterBased())
                                <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ count($this->getChapters()) }} chapters
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Panel -->
            @if($resourceType === 'resource')
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Progress</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ round($completionPercentage) }}%</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Complete</div>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div
                                class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-500"
                                style="width: {{ $completionPercentage }}%"
                            ></div>
                        </div>
                        @if(!empty($progressMarkers))
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                @foreach([25, 50, 75, 100] as $milestone)
                                    <div class="text-center text-xs p-2 rounded {{ in_array($milestone, $progressMarkers) ? 'bg-green-50 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                        {{ $milestone }}%
                                        @if(in_array($milestone, $progressMarkers))
                                            <div class="text-green-500">✓</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Chapters -->
        @if($this->isChapterBased())
            <div class="mt-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Chapters</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->getChapters() as $index => $chapter)
                        <button
                            wire:click="jumpToChapter({{ $index }})"
                            class="w-full text-left px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 {{ $currentChapter === $index ? 'bg-blue-50 dark:bg-blue-900 border-r-4 border-blue-500' : '' }}"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-sm font-medium {{ $currentChapter === $index ? 'bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400' }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $chapter['title'] }}</h4>
                                        @if(!empty($chapter['description']))
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $chapter['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($chapter['duration'] > 0)
                                    <div class="flex-shrink-0 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                        {{ $this->formatTime($chapter['duration']) }}
                                    </div>
                                @endif
                            </div>
                        </button>
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
            function videoPlayerData(currentTime, duration, isPlaying) {
                return {
                    player: null,
                    playerReady: false,
                    currentTime,
                    duration,
                    isPlaying,

                    initPlayer(playerId, source) {
                        if (!source || !source.url) return;

                        this.player = videojs(playerId, {
                            responsive: true,
                            fluid: false,
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
                            $wire.call('updateTime', this.currentTime, this.duration);
                        });

                        this.player.on('play', () => {
                            this.isPlaying = true;
                            $wire.call('setPlaying');
                        });

                        this.player.on('pause', () => {
                            this.isPlaying = false;
                            $wire.call('setPaused');
                        });

                        this.player.on('ended', () => {
                            this.isPlaying = false;
                            $wire.call('handleEnded');
                        });

                        this.player.on('volumechange', () => {
                            $wire.call('updateVolume', this.player.volume());
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
                    const player = videojs(document.querySelector('.video-js')?.id);
                    if (player) {
                        player.playbackRate(event.rate);
                    }
                });

                Livewire.on('toggleCaptions', (event) => {
                    const player = videojs(document.querySelector('.video-js')?.id);
                    if (player) {
                        const tracks = player.textTracks();
                        if (tracks.length > 0) {
                            tracks[0].mode = event.show ? 'showing' : 'disabled';
                        }
                    }
                });

                Livewire.on('seekTo', (event) => {
                    const player = videojs(document.querySelector('.video-js')?.id);
                    if (player) {
                        player.currentTime(event.time);
                        if (event.autoplay !== false) {
                            player.play().catch(e => console.warn('Seek play failed:', e));
                        }
                    }
                });

                Livewire.on('loadNewSource', (event) => {
                    const player = videojs(document.querySelector('.video-js')?.id);
                    if (player && event.source) {
                        player.src(event.source);
                        player.load();
                        if (event.autoplay !== false) {
                            player.play().catch(e => console.warn('Source load play failed:', e));
                        }
                    }
                });
            });
        </script>

    </section>
</div>
