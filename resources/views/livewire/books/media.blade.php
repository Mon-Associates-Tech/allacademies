<div class="mt-8 space-y-8">
    <!-- Section Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl mb-6 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.5-4.5a3 3 0 011.5 2.6V6a3 3 0 01-3 3h-6a3 3 0 01-3-3V4a3 3 0 013-3h2.25a3 3 0 012.25 3z"/>
            </svg>
        </div>
        <h2 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-gray-900 bg-clip-text text-transparent mb-4">
            🎧📺 Media Library
        </h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Immerse yourself in rich multimedia content designed to enhance your learning experience
        </p>
    </div>

    @if($book->single_video_file)
        <!-- Single Video Section -->
        <div class="relative overflow-hidden bg-white rounded-3xl shadow-2xl ring-1 ring-gray-200/50 hover:shadow-3xl transition-all duration-500 group">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-purple-500/5"></div>
            <div class="relative p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">📺 Complete Video Experience</h3>
                    <div class="ml-auto bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-semibold">
                        Full Length
                    </div>
                </div>

                <!-- Custom Video Player -->
                <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl max-w-4xl mx-auto">
                    <video
                        class="w-full aspect-video"
                        preload="metadata"
                        id="mainVideo"
                        x-data="videoPlayer()"
                        x-ref="video"
                        @loadedmetadata="onLoadedMetadata"
                        @timeupdate="onTimeUpdate"
                        @ended="onEnded"
                        wire:ignore
                    >
                        <source src="{{ asset('storage/' . $book->single_video_file) }}" type="video/mp4">
                    </video>

                    <!-- Hover overlay for center play button -->
                    <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-all duration-300 flex items-center justify-center"
                         x-show="!$wire.isVideoPlaying">
                        <button class="flex items-center justify-center w-20 h-20 bg-white/90 backdrop-blur-sm rounded-full shadow-2xl hover:scale-110 hover:bg-white transition-all duration-300"
                                wire:click="toggleMainVideo">
                            <svg class="w-8 h-8 text-gray-800 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 5v10l7-5L8 5z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Always Visible Video Controls -->
                    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-4 mt-4 shadow-xl max-w-4xl mx-auto">
                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="h-2 bg-white/10 rounded-full overflow-hidden cursor-pointer"
                                 x-on:click="seekToPosition($event)">
                                <div class="h-full bg-gradient-to-r from-red-400 to-pink-400 rounded-full transition-all duration-300"
                                     :style="`width: ${progressPercent}%`"></div>
                            </div>
                            <div class="flex justify-between text-white/70 text-sm mt-2">
                                <span x-text="formatTime($wire.currentVideoTime)">0:00</span>
                                <span x-text="formatTime(duration)">0:00</span>
                            </div>
                        </div>

                        <!-- Main Controls -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <!-- Previous/Rewind -->
                                <button class="p-2 hover:bg-white/10 rounded-lg transition-colors"
                                        wire:click="rewindVideo">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"/>
                                    </svg>
                                </button>

                                <!-- Play/Pause -->
                                <button class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-red-400 to-pink-400 rounded-full shadow-2xl hover:scale-110 transition-all duration-300"
                                        wire:click="toggleMainVideo">
                                    @if($isVideoPlaying)
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6 4h4v12H6V4zm8 0h4v12h-4V4z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 5v10l7-5L8 5z"/>
                                        </svg>
                                    @endif
                                </button>

                                <!-- Next/Forward -->
                                <button class="p-2 hover:bg-white/10 rounded-lg transition-colors"
                                        wire:click="forwardVideo">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4.555 5.168A1 1 0 003 6v8a1 1 0 001.555.832L10 11.202V14a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4A1 1 0 0010 6v2.798l-5.445-3.63z"/>
                                    </svg>
                                </button>

                                <!-- Volume Control -->
                                <div class="flex items-center space-x-2">
                                    <button x-on:click="toggleMute()">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"
                                             x-show="!isMuted">
                                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.816L4.7 13.7A1 1 0 014 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.7a1 1 0 01.7-.3l3.683-3.116zM12 8a1 1 0 011.414.586l2 3a1 1 0 010 .828l-2 3A1 1 0 0112 14v-3a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"
                                             x-show="isMuted" style="display: none;">
                                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.816L4.7 13.7A1 1 0 014 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.7a1 1 0 01.7-.3l3.683-3.116zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <div class="w-16 h-1 bg-white/10 rounded-full cursor-pointer" x-on:click="setVolume($event)">
                                        <div class="h-full bg-white rounded-full" :style="`width: ${$wire.videoVolume}%`"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side Controls -->
                            <div class="flex items-center space-x-2">
                                <!-- Speed Control -->
                                <select class="bg-white/10 text-white text-xs rounded px-2 py-1 border-0 focus:ring-2 focus:ring-red-400"
                                        wire:model="videoSpeed" wire:change="updateVideoSpeed($event.target.value)">
                                    <option value="0.5">0.5x</option>
                                    <option value="0.75">0.75x</option>
                                    <option value="1">1x</option>
                                    <option value="1.25">1.25x</option>
                                    <option value="1.5">1.5x</option>
                                    <option value="2">2x</option>
                                </select>

                                <!-- Fullscreen -->
                                <button class="p-2 hover:bg-white/10 rounded-lg transition-colors" x-on:click="toggleFullscreen()">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($book->single_audio_file)
        <!-- Single Audio Section -->
        <div class="relative overflow-hidden bg-white rounded-3xl shadow-2xl ring-1 ring-gray-200/50 hover:shadow-3xl transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 to-teal-500/5"></div>
            <div class="relative p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">🎧 Full Audio Journey</h3>
                    <div class="ml-auto bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold">
                        Complete
                    </div>
                </div>

                <!-- Custom Audio Player -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 shadow-2xl"
                     x-data="audioPlayer()" wire:ignore>
                    <audio x-ref="audio" preload="metadata"
                           @loadedmetadata="onLoadedMetadata"
                           @timeupdate="onTimeUpdate"
                           @ended="onEnded">
                        <source src="{{ asset('storage/' . $book->single_audio_file) }}" type="audio/mpeg">
                    </audio>

                    <!-- Audio Visualizer -->
                    <div class="flex items-center justify-center space-x-1 mb-6">
                        @for($i = 0; $i < 10; $i++)
                            <div class="w-1 bg-gradient-to-t from-green-400 to-teal-400 rounded-full animate-pulse"
                                 style="height: {{ rand(15, 45) }}px; animation-delay: {{ $i * 0.1 }}s"></div>
                        @endfor
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="h-3 bg-white/10 rounded-full overflow-hidden cursor-pointer" x-on:click="seekToPosition($event)">
                            <div class="h-full bg-gradient-to-r from-green-400 to-teal-400 rounded-full transition-all duration-300"
                                 :style="`width: ${progressPercent}%`"></div>
                        </div>
                        <div class="flex justify-between text-white/70 text-sm mt-2">
                            <span x-text="formatTime($wire.currentAudioTime)">0:00</span>
                            <span x-text="formatTime(duration)">0:00</span>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center justify-center space-x-6">
                        <button class="p-3 hover:bg-white/10 rounded-full transition-colors" x-on:click="rewind()">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"/>
                            </svg>
                        </button>
                        <button class="flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-400 to-teal-400 rounded-full shadow-2xl hover:scale-110 transition-all duration-300"
                                wire:click="toggleMainAudio">
                            @if($isAudioPlaying)
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6 4h4v12H6V4zm8 0h4v12h-4V4z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 5v10l7-5L8 5z"/>
                                </svg>
                            @endif
                        </button>
                        <button class="p-3 hover:bg-white/10 rounded-full transition-colors" x-on:click="forward()">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4.555 5.168A1 1 0 003 6v8a1 1 0 001.555.832L10 11.202V14a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4A1 1 0 0010 6v2.798l-5.445-3.63z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Additional Controls -->
                    <div class="flex items-center justify-between mt-6 pt-6 border-t border-white/10">
                        <div class="flex items-center space-x-4">
                            <button class="text-white/70 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                            <button class="text-white/70 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button x-on:click="toggleMute()">
                                <svg class="w-5 h-5 text-white/70" fill="currentColor" viewBox="0 0 20 20" x-show="!isMuted">
                                    <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.816L4.7 13.7A1 1 0 014 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.7a1 1 0 01.7-.3l3.683-3.116z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div class="w-20 h-2 bg-white/10 rounded-full cursor-pointer" x-on:click="setVolume($event)">
                                <div class="h-full bg-white/50 rounded-full" :style="`width: ${$wire.audioVolume}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($book->chapter_videos && count($book->chapter_videos))
        <!-- Chapter Videos Section -->
        <div class="bg-white rounded-3xl shadow-2xl ring-1 ring-gray-200/50 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500/5 to-indigo-500/5 p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">📺 Chapter Videos</h3>
                    </div>
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold">
                        {{ $chapterVideosCount }} Videos
                    </div>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($book->chapter_videos as $index => $video)
                        <div class="group relative bg-white rounded-2xl shadow-lg ring-1 ring-gray-200/50 p-6 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-3 py-1.5 rounded-full text-sm font-semibold shadow-lg">
                                    Chapter {{ $index + 1 }}
                                </span>
                                <span class="text-gray-500 text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ isset($chapterVideoStates[$index]) ? gmdate("i:s", $chapterVideoStates[$index]['duration']) : '12:34' }}
                                </span>
                            </div>

                            <!-- Custom Chapter Video Player -->
                            <div class="relative bg-black rounded-xl overflow-hidden shadow-lg"
                                 x-data="chapterVideoPlayer({{ $index }})" wire:ignore>
                                <video class="w-full aspect-video" preload="metadata"
                                       x-ref="video"
                                       @loadedmetadata="onLoadedMetadata"
                                       @timeupdate="onTimeUpdate"
                                       @ended="onEnded">
                                    <source src="{{ asset('storage/' . $video) }}" type="video/mp4">
                                </video>

                                <!-- Hover Controls -->
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center"
                                     x-show="!isPlaying">
                                    <button class="flex items-center justify-center w-16 h-16 bg-white/90 backdrop-blur-sm rounded-full shadow-2xl hover:scale-110 transition-all duration-300"
                                            wire:click="toggleChapterVideo({{ $index }})">
                                        <svg class="w-6 h-6 text-gray-800 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 5v10l7-5L8 5z"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Chapter Video Controls -->
                                <div class="bg-gradient-to-r from-gray-800 to-gray-700 rounded-xl p-4 mt-3">
                                    <div class="mb-3">
                                        <div class="h-2 bg-white/10 rounded-full overflow-hidden cursor-pointer"
                                             x-on:click="seekToPosition($event)">
                                            <div class="h-full bg-gradient-to-r from-purple-400 to-pink-400 rounded-full"
                                                 :style="`width: ${progressPercent}%`"></div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <button class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full shadow-lg hover:scale-110 transition-all duration-300"
                                                    wire:click="toggleChapterVideo({{ $index }})">
                                                @if(isset($chapterVideoStates[$index]) && $chapterVideoStates[$index]['isPlaying'])
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M6 4h4v12H6V4zm8 0h4v12h-4V4z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8 5v10l7-5L8 5z"/>
                                                    </svg>
                                                @endif
                                            </button>

                                            <div class="flex items-center space-x-2">
                                                <button x-on:click="toggleMute()">
                                                    <svg class="w-4 h-4 text-white/70" fill="currentColor" viewBox="0 0 20 20" x-show="!isMuted">
                                                        <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.816L4.7 13.7A1 1 0 014 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.7a1 1 0 01.7-.3l3.683-3.116z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                                <div class="w-16 h-1 bg-white/10 rounded-full cursor-pointer" x-on:click="setVolume($event)">
                                                    <div class="w-3/4 h-full bg-white/50 rounded-full" :style="`width: ${volume}%`"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-2 text-white/70 text-xs">
                                            <span x-text="`${formatTime(currentTime)} / ${formatTime(duration)}`">0:00 / 0:00</span>
                                            <button class="p-1 hover:bg-white/10 rounded transition-colors" x-on:click="toggleFullscreen()">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="font-bold text-gray-800">Chapter {{ $index + 1 }} Overview</h4>
                                <p class="text-gray-600 text-sm mt-1">Essential concepts and key insights</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($book->chapter_audios && count($book->chapter_audios))
        <!-- Chapter Audios Section -->
        <div class="bg-white rounded-3xl shadow-2xl ring-1 ring-gray-200/50 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500/5 to-cyan-500/5 p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-emerald-500 to-cyan-500 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">🎧 Chapter Audios</h3>
                    </div>
                    <div class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded-full text-sm font-semibold">
                        {{ $chapterAudiosCount }} Tracks
                    </div>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach($book->chapter_audios as $index => $audio)
                        <div class="group bg-white rounded-2xl shadow-lg ring-1 ring-gray-200/50 p-6 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-gradient-to-r from-teal-500 to-cyan-500 text-white px-3 py-1.5 rounded-full text-sm font-semibold shadow-lg">
                                    Chapter {{ $index + 1 }}
                                </span>
                                <span class="text-gray-500 text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ isset($chapterAudioStates[$index]) ? gmdate("i:s", $chapterAudioStates[$index]['duration']) : '25:30' }}
                                </span>
                            </div>

                            <!-- Custom Chapter Audio Player -->
                            <div class="bg-gradient-to-br from-gray-800 to-gray-700 rounded-xl p-6"
                                 x-data="chapterAudioPlayer({{ $index }})" wire:ignore>
                                <audio x-ref="audio" preload="metadata"
                                       @loadedmetadata="onLoadedMetadata"
                                       @timeupdate="onTimeUpdate"
                                       @ended="onEnded">
                                    <source src="{{ asset('storage/' . $audio) }}" type="audio/mpeg">
                                </audio>

                                <!-- Mini Audio Visualizer -->
                                <div class="flex items-center justify-center space-x-1 mb-4">
                                    @for($i = 0; $i < 5; $i++)
                                        <div class="w-0.5 bg-gradient-to-t from-teal-400 to-cyan-400 rounded-full animate-pulse"
                                             style="height: {{ [12, 20, 8, 25, 15][$i] }}px; animation-delay: {{ $i * 0.1 }}s"></div>
                                    @endfor
                                </div>

                                <!-- Progress & Controls -->
                                <div class="mb-4">
                                    <div class="h-2 bg-white/10 rounded-full overflow-hidden cursor-pointer"
                                         x-on:click="seekToPosition($event)">
                                        <div class="h-full bg-gradient-to-r from-teal-400 to-cyan-400 rounded-full"
                                             :style="`width: ${progressPercent}%`"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-white/70 text-sm" x-text="formatTime(currentTime)">0:00</span>
                                    <button class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-teal-400 to-cyan-400 rounded-full shadow-lg hover:scale-110 transition-all duration-300"
                                            wire:click="toggleChapterAudio({{ $index }})">
                                        @if(isset($chapterAudioStates[$index]) && $chapterAudioStates[$index]['isPlaying'])
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6 4h4v12H6V4zm8 0h4v12h-4V4z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 5v10l7-5L8 5z"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <span class="text-white/70 text-sm" x-text="formatTime(duration)">0:00</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="font-bold text-gray-800">Chapter {{ $index + 1 }} Audio</h4>
                                <p class="text-gray-600 text-sm mt-1">Deep dive into core principles and applications</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(!$hasMedia)
        <!-- No Media Fallback -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl shadow-2xl border-l-8 border-amber-400">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-200/20 rounded-full -translate-y-8 translate-x-8"></div>
            <div class="relative p-8">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-r from-amber-400 to-orange-400 rounded-2xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Media Coming Soon! 🚀</h3>
                        <p class="text-gray-700 text-lg leading-relaxed mb-6">
                            We're crafting an amazing multimedia experience for this book. Exciting videos, immersive audio content, and interactive materials are on their way!
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <button class="bg-gradient-to-r from-amber-400 to-orange-400 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 shadow-lg"
                                    wire:click="$dispatch('notify-when-ready')">
                                Notify Me When Ready
                            </button>
                            <button class="bg-white text-amber-600 px-6 py-3 rounded-xl font-semibold hover:shadow-lg border-2 border-amber-200 hover:border-amber-300 transition-all duration-300"
                                    wire:click="$dispatch('preview-content')">
                                Preview Content
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Video Player Alpine.js Component
    function videoPlayer() {
        return {
            duration: 0,
            progressPercent: 0,
            isMuted: false,

            onLoadedMetadata() {
                this.duration = this.$refs.video.duration;
            },

            onTimeUpdate() {
                this.progressPercent = (this.$refs.video.currentTime / this.duration) * 100;
                @this.call('updateProgress', 'video', this.$refs.video.currentTime, this.duration);
            },

            onEnded() {
                @this.set('isVideoPlaying', false);
            },

            seekToPosition(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                this.$refs.video.currentTime = percentage * this.duration;
            },

            toggleMute() {
                this.isMuted = !this.isMuted;
                this.$refs.video.muted = this.isMuted;
            },

            setVolume(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                this.$refs.video.volume = percentage;
                @this.call('updateVideoVolume', percentage * 100);
            },

            toggleFullscreen() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    this.$refs.video.requestFullscreen().catch(err => {
                        console.log('Error attempting to enable fullscreen:', err.message);
                    });
                }
            },

            formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = Math.floor(seconds % 60);
                return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }
        }
    }

    // Audio Player Alpine.js Component
    function audioPlayer() {
        return {
            duration: 0,
            progressPercent: 0,
            isMuted: false,

            onLoadedMetadata() {
                this.duration = this.$refs.audio.duration;
            },

            onTimeUpdate() {
                this.progressPercent = (this.$refs.audio.currentTime / this.duration) * 100;
                @this.call('updateProgress', 'audio', this.$refs.audio.currentTime, this.duration);
            },

            onEnded() {
                @this.set('isAudioPlaying', false);
            },

            seekToPosition(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                this.$refs.audio.currentTime = percentage * this.duration;
            },

            rewind() {
                this.$refs.audio.currentTime = Math.max(0, this.$refs.audio.currentTime - 10);
            },

            forward() {
                this.$refs.audio.currentTime = Math.min(this.duration, this.$refs.audio.currentTime + 10);
            },

            toggleMute() {
                this.isMuted = !this.isMuted;
                this.$refs.audio.muted = this.isMuted;
            },

            setVolume(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                this.$refs.audio.volume = percentage;
                @this.call('updateAudioVolume', percentage * 100);
            },

            formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = Math.floor(seconds % 60);
                return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }
        }
    }

    // Chapter Video Player Alpine.js Component
    function chapterVideoPlayer(index) {
        return {
            index: index,
            duration: 0,
            currentTime: 0,
            progressPercent: 0,
            isPlaying: false,
            isMuted: false,
            volume: 75,

            onLoadedMetadata() {
                this.duration = this.$refs.video.duration;
                @this.call('updateChapterProgress', 'video', this.index, 0, this.duration);
            },

            onTimeUpdate() {
                this.currentTime = this.$refs.video.currentTime;
                this.progressPercent = (this.currentTime / this.duration) * 100;
                @this.call('updateChapterProgress', 'video', this.index, this.currentTime, this.duration);
            },

            onEnded() {
                this.isPlaying = false;
                @this.call('toggleChapterVideo', this.index);
            },

            seekToPosition(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                this.$refs.video.currentTime = percentage * this.duration;
            },

            toggleMute() {
                this.isMuted = !this.isMuted;
                this.$refs.video.muted = this.isMuted;
            },

            setVolume(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                this.volume = percentage * 100;
                this.$refs.video.volume = percentage;
            },

            toggleFullscreen() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    this.$refs.video.requestFullscreen().catch(err => {
                        console.log('Error attempting to enable fullscreen:', err.message);
                    });
                }
            },

            formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = Math.floor(seconds % 60);
                return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }
        }
    }

    // Chapter Audio Player Alpine.js Component
    function chapterAudioPlayer(index) {
        return {
            index: index,
            duration: 0,
            currentTime: 0,
            progressPercent: 0,
            isPlaying: false,

            onLoadedMetadata() {
                this.duration = this.$refs.audio.duration;
                @this.call('updateChapterProgress', 'audio', this.index, 0, this.duration);
            },

            onTimeUpdate() {
                this.currentTime = this.$refs.audio.currentTime;
                this.progressPercent = (this.currentTime / this.duration) * 100;
                @this.call('updateChapterProgress', 'audio', this.index, this.currentTime, this.duration);
            },

            onEnded() {
                this.isPlaying = false;
                @this.call('toggleChapterAudio', this.index);
            },

            seekToPosition(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const percentage = clickX / rect.width;
                this.$refs.audio.currentTime = percentage * this.duration;
            },

            formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = Math.floor(seconds % 60);
                return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }
        }
    }

    // Livewire event listeners
    document.addEventListener('livewire:init', () => {
        Livewire.on('toggle-main-video', (isPlaying) => {
            const video = document.getElementById('mainVideo');
            if (video) {
                if (isPlaying) {
                    video.play();
                } else {
                    video.pause();
                }
            }
        });

        Livewire.on('toggle-main-audio', (isPlaying) => {
            const audio = document.querySelector('#mainAudio');
            if (audio) {
                if (isPlaying) {
                    audio.play();
                } else {
                    audio.pause();
                }
            }
        });

        Livewire.on('toggle-chapter-video', (data) => {
            const video = document.querySelector(`[x-data*="chapterVideoPlayer(${data.index})"] video`);
            if (video) {
                if (data.isPlaying) {
                    video.play();
                } else {
                    video.pause();
                }
            }
        });

        Livewire.on('toggle-chapter-audio', (data) => {
            const audio = document.querySelector(`[x-data*="chapterAudioPlayer(${data.index})"] audio`);
            if (audio) {
                if (data.isPlaying) {
                    audio.play();
                } else {
                    audio.pause();
                }
            }
        });

        Livewire.on('seek-video', (time) => {
            const video = document.getElementById('mainVideo');
            if (video) {
                video.currentTime = time;
            }
        });

        Livewire.on('seek-audio', (time) => {
            const audio = document.querySelector('#mainAudio');
            if (audio) {
                audio.currentTime = time;
            }
        });

        Livewire.on('rewind-video', (seconds) => {
            const video = document.getElementById('mainVideo');
            if (video) {
                video.currentTime = Math.max(0, video.currentTime - seconds);
            }
        });

        Livewire.on('forward-video', (seconds) => {
            const video = document.getElementById('mainVideo');
            if (video) {
                video.currentTime = Math.min(video.duration, video.currentTime + seconds);
            }
        });

        Livewire.on('update-video-volume', (volume) => {
            const video = document.getElementById('mainVideo');
            if (video) {
                video.volume = volume / 100;
            }
        });

        Livewire.on('update-audio-volume', (volume) => {
            const audio = document.querySelector('#mainAudio');
            if (audio) {
                audio.volume = volume / 100;
            }
        });

        Livewire.on('update-video-speed', (speed) => {
            const video = document.getElementById('mainVideo');
            if (video) {
                video.playbackRate = parseFloat(speed);
            }
        });
    });
</script>
