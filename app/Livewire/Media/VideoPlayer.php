<?php

namespace App\Livewire\Media;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class VideoPlayer extends Component
{
    public $playerId;
    public $currentTime = 0;
    public $duration = 0;
    public $isPlaying = false;
    public $playbackRate = 1;
    public $volume = 1;
    public $showCaptions = false;
    public $currentChapter = 0;

    // Progress tracking
    public $progressMarkers = [];
    public $completionPercentage = 0;

    // Media data resolved from resource or direct URL
    public $mediaData = [];
    public $resourceType = null; // 'resource', 'url', 'file'
    public $resource = null;

    protected $listeners = [
        'playerTimeUpdate' => 'updateTime',
        'playerPlay' => 'setPlaying',
        'playerPause' => 'setPaused',
        'playerEnded' => 'handleEnded',
        'playerVolumeChange' => 'updateVolume',
    ];

    public function mount($resource = null, $url = null, $type = 'video'): void
    {
        $this->playerId = 'media-player-' . uniqid();
        $this->resolveMediaSource($resource, $url, $type);
    }

    public function render()
    {
        return view('livewire.media.video-player');
    }

    /**
     * Resolve media source from various input types
     */
    private function resolveMediaSource($resource, $url, $type)
    {
        if ($resource instanceof Model) {
            $this->resourceType = 'resource';
            $this->resource = $resource;
            $this->mediaData = $this->buildMediaDataFromResource($resource, $type);
        } elseif ($url) {
            $this->resourceType = 'url';
            $this->mediaData = $this->buildMediaDataFromUrl($url, $type);
        } else {
            throw new \InvalidArgumentException('Either resource or url must be provided');
        }
    }

    /**
     * Build media data from a resource (Book, Post, etc.)
     */
    private function buildMediaDataFromResource(Model $resource, $type): array
    {
        $media = $resource->media ?? null;

        if (!$media) {
            return [];
            throw new \InvalidArgumentException('Resource must have media relationship');
        }

        $mediaData = [
            'title' => $resource->title ?? $resource->name ?? 'Untitled',
            'description' => $resource->description ?? '',
            'type' => $type,
            'chapters' => [],
            'playlist' => [],
            'current_source' => null,
            'captions_url' => null,
            'thumbnail_url' => null,
        ];

        // Handle different media types
        if ($type === 'video') {
            if ($media->single_video) {
                // Single video
                $mediaData['current_source'] = [
                    'url' => $media->single_video,
                    'type' => $this->getMimeType($media->single_video),
                    'chapter' => null,
                ];
                $mediaData['captions_url'] = $media->single_video_captions ?? null;
                $mediaData['thumbnail_url'] = $media->single_video_thumbnail ?? null;
            } elseif ($media->chapter_videos) {
                // Chapter videos
                $chapters = is_string($media->chapter_videos)
                    ? json_decode($media->chapter_videos, true)
                    : $media->chapter_videos;

                $mediaData['chapters'] = $this->buildChaptersFromArray($chapters, 'video');
                $mediaData['current_source'] = $mediaData['chapters'][0] ?? null;
                $mediaData['playlist'] = $mediaData['chapters'];
            }
        } elseif ($type === 'audio') {
            if ($media->single_audio) {
                // Single audio
                $mediaData['current_source'] = [
                    'url' => $media->single_audio,
                    'type' => $this->getMimeType($media->single_audio),
                    'chapter' => null,
                ];
                $mediaData['captions_url'] = $media->single_audio_captions ?? null;
                $mediaData['thumbnail_url'] = $media->single_audio_thumbnail ?? null;
            } elseif ($media->chapter_audios) {
                // Chapter audios
                $chapters = is_string($media->chapter_audios)
                    ? json_decode($media->chapter_audios, true)
                    : $media->chapter_audios;

                $mediaData['chapters'] = $this->buildChaptersFromArray($chapters, 'audio');
                $mediaData['current_source'] = $mediaData['chapters'][0] ?? null;
                $mediaData['playlist'] = $mediaData['chapters'];
            }
        }

        return $mediaData;
    }

    /**
     * Build media data from direct URL
     */
    private function buildMediaDataFromUrl($url, $type)
    {
        return [
            'title' => basename($url),
            'description' => '',
            'type' => $type,
            'chapters' => [],
            'playlist' => [],
            'current_source' => [
                'url' => $url,
                'type' => $this->getMimeType($url),
                'chapter' => null,
            ],
            'captions_url' => null,
            'thumbnail_url' => null,
        ];
    }

    /**
     * Build chapters array from media data
     */
    private function buildChaptersFromArray($chapters, $type)
    {
        $result = [];

        foreach ($chapters as $index => $chapter) {
            $sourceKey = $type === 'video' ? 'single_video' : 'single_audio';

            if (isset($chapter[$sourceKey])) {
                $result[] = [
                    'chapter' => $chapter['chapter'] ?? ($index + 1),
                    'title' => $chapter['title'] ?? "Chapter " . ($index + 1),
                    'description' => $chapter['description'] ?? '',
                    'url' => $chapter[$sourceKey],
                    'type' => $this->getMimeType($chapter[$sourceKey]),
                    'captions_url' => $chapter['captions'] ?? null,
                    'thumbnail_url' => $chapter['thumbnail'] ?? null,
                    'duration' => $chapter['duration'] ?? 0,
                ];
            }
        }

        return $result;
    }

    /**
     * Get MIME type from URL/file extension
     */
    private function getMimeType($url)
    {
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));

        $mimeTypes = [
            // Video
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'avi' => 'video/avi',
            'mov' => 'video/quicktime',
            // Audio
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            'aac' => 'audio/aac',
        ];

        return $mimeTypes[$extension] ?? ($this->mediaData['type'] === 'video' ? 'video/mp4' : 'audio/mpeg');
    }

    // Player Control Methods
    public function updateTime($currentTime, $duration = null)
    {
        $this->currentTime = $currentTime;
        if ($duration) {
            $this->duration = $duration;
        }

        $this->calculateProgress();
        $this->trackProgress();
    }

    public function setPlaying()
    {
        $this->isPlaying = true;
        $this->logEvent('play');
    }

    public function setPaused()
    {
        $this->isPlaying = false;
        $this->logEvent('pause');
    }

    public function handleEnded()
    {
        $this->isPlaying = false;
        $this->logEvent('complete');

        // Auto-advance to next chapter if available
        if ($this->hasNextChapter()) {
            $this->playNextChapter();
        }
    }

    public function updateVolume($volume)
    {
        $this->volume = $volume;
    }

    public function changePlaybackRate($rate)
    {
        $this->playbackRate = $rate;
        $this->dispatch('setPlaybackRate', rate: $rate);
    }

    public function toggleCaptions()
    {
        $this->showCaptions = !$this->showCaptions;
        $this->dispatch('toggleCaptions', show: $this->showCaptions);
    }

    // Chapter/Playlist Navigation
    public function jumpToChapter($chapterIndex)
    {
        if (isset($this->mediaData['chapters'][$chapterIndex])) {
            $this->currentChapter = $chapterIndex;
            $this->mediaData['current_source'] = $this->mediaData['chapters'][$chapterIndex];
            $this->dispatch('loadNewSource', source: $this->mediaData['current_source']);
        }
    }

    public function playNextChapter()
    {
        if ($this->hasNextChapter()) {
            $this->jumpToChapter($this->currentChapter + 1);
        }
    }

    public function playPreviousChapter()
    {
        if ($this->hasPreviousChapter()) {
            $this->jumpToChapter($this->currentChapter - 1);
        }
    }

    public function hasNextChapter()
    {
        return !empty($this->mediaData['chapters']) &&
            $this->currentChapter < count($this->mediaData['chapters']) - 1;
    }

    public function hasPreviousChapter()
    {
        return !empty($this->mediaData['chapters']) && $this->currentChapter > 0;
    }

    public function seekTo($time)
    {
        $this->dispatch('seekTo', time: $time);
    }

    // Helper Methods
    private function calculateProgress()
    {
        if ($this->duration > 0) {
            $this->completionPercentage = ($this->currentTime / $this->duration) * 100;
        }
    }

    private function trackProgress()
    {
        $milestones = [25, 50, 75, 100];

        foreach ($milestones as $milestone) {
            if ($this->completionPercentage >= $milestone &&
                !in_array($milestone, $this->progressMarkers)) {
                $this->progressMarkers[] = $milestone;
                $this->logEvent('progress', ['milestone' => $milestone]);
            }
        }
    }

    private function logEvent($event, $data = [])
    {
        if ($this->resourceType === 'resource' && $this->resource) {
            // Log events for resource-based media
            // MediaAnalytics::log($event, $this->resource, auth()->user(), array_merge($data, [
            //     'current_time' => $this->currentTime,
            //     'chapter' => $this->currentChapter,
            //     'media_type' => $this->mediaData['type'],
            // ]));
        }
    }

    public function getPlaybackRateOptions()
    {
        return [
            0.5 => '0.5x',
            0.75 => '0.75x',
            1 => 'Normal',
            1.25 => '1.25x',
            1.5 => '1.5x',
            2 => '2x'
        ];
    }

    public function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = floor($seconds % 60);

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    // Computed properties for view
    public function getCurrentSource()
    {
        return $this->mediaData['current_source'] ?? null;
    }

    public function getTitle()
    {
        return $this->mediaData['title'] ?? 'Untitled Media';
    }

    public function getDescription()
    {
        return $this->mediaData['description'] ?? '';
    }

    public function getChapters()
    {
        return $this->mediaData['chapters'] ?? [];
    }

    public function getCaptionsUrl()
    {
        if(isset($this->mediaData['current_source']['captions_url'])){
            return $this->mediaData['current_source']['captions_url'];
        }
        elseif(isset($this->mediaData['captions_url'])){
            return $this->mediaData['captions_url'];
        }
        return '';
    }

    public function getThumbnailUrl()
    {
        return $this->mediaData['current_source']['thumbnail_url'] ?? $this->mediaData['thumbnail_url'];
    }

    public function isChapterBased()
    {
        return !empty($this->mediaData['chapters']);
    }
}
