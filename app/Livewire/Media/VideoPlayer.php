<?php

namespace App\Livewire\Media;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

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

    public $chapterDurations = [];

    public $completedChapters = [];

    protected $listeners = [
        'playerTimeUpdate' => 'updateTime',
        'playerPlay' => 'setPlaying',
        'playerPause' => 'setPaused',
        'playerEnded' => 'handleEnded',
        'playerVolumeChange' => 'updateVolume',
    ];

    public function mount($resource = null, $url = null): void
    {
        $this->playerId = 'video-player-'.uniqid();
        $this->resolveMediaSource($resource, $url);
    }

    public function render()
    {
        return view('livewire.media.video-player');
    }

    private function resolveMediaSource($resource, $url)
    {
        if ($resource instanceof Model) {
            $this->resourceType = 'resource';
            $this->resource = $resource;
            $this->mediaData = $this->buildMediaDataFromResource($resource);
        } elseif ($url) {
            $this->resourceType = 'url';
            $this->mediaData = $this->buildMediaDataFromUrl($url);
        } else {
            throw new \InvalidArgumentException('Either resource or url must be provided');
        }
    }

    private function buildMediaDataFromResource(Model $resource): array
    {
        $mediaData = [
            'title' => $resource->title ?? $resource->name ?? 'Untitled',
            'description' => $resource->description ?? '',
            'chapters' => [],
            'current_source' => null,
            'thumbnail_url' => null,
        ];

        $toc = $resource->table_of_contents ?? [];
        $singleVideo = $resource->single_video;
        $chapterVideos = $resource->chapter_videos;

        if ($singleVideo) {
            $mediaData['current_source'] = [
                'url' => $singleVideo,
                'type' => $this->getMimeType($singleVideo),
                'chapter' => null,
            ];
        } elseif (!empty($chapterVideos)) {
            $mediaData['chapters'] = $this->buildChaptersFromArray($chapterVideos, $toc);
            $mediaData['current_source'] = $mediaData['chapters'][0] ?? null;
        }

        return $mediaData;
    }

    private function buildMediaDataFromUrl($url): array
    {
        return [
            'title' => basename($url),
            'description' => '',
            'chapters' => [],
            'current_source' => [
                'url' => $url,
                'type' => $this->getMimeType($url),
                'chapter' => null,
            ],
            'thumbnail_url' => null,
        ];
    }

    private function buildChaptersFromArray($chapters, $toc = []): array
    {
        $result = [];

        foreach ($chapters as $index => $item) {
            if (is_array($item) && isset($item['file'])) {
                $chapterNumber = $item['chapter'] ?? ($index + 1);
                $tocChapter = collect($toc)->firstWhere('chapter', $chapterNumber) ?? [];

                $result[] = [
                    'chapter' => $chapterNumber,
                    'title' => $item['title'] ?? $tocChapter['title'] ?? "Chapter {$chapterNumber}",
                    'description' => $item['description'] ?? $tocChapter['description'] ?? '',
                    'url' => $item['url'] ?? (str_starts_with($item['file'], 'http') ? $item['file'] : asset('storage/'.$item['file'])),
                    'type' => $this->getMimeType($item['file']),
                    'captions_url' => $item['captions'] ?? null,
                    'thumbnail_url' => $item['thumbnail'] ?? null,
                    'duration' => $item['duration'] ?? 0,
                ];
                continue;
            }

            $url = is_array($item) ? ($item['url'] ?? $item['single_video'] ?? null) : $item;
            if (!$url) continue;

            $tocChapter = $toc[$index] ?? [];
            $chapterNumber = $tocChapter['chapter'] ?? ($index + 1);

            $result[] = [
                'chapter' => $chapterNumber,
                'title' => is_array($item) ? ($item['title'] ?? $tocChapter['title'] ?? "Chapter {$chapterNumber}") : ($tocChapter['title'] ?? "Chapter {$chapterNumber}"),
                'description' => is_array($item) ? ($item['description'] ?? $tocChapter['description'] ?? '') : ($tocChapter['description'] ?? ''),
                'url' => $url,
                'type' => $this->getMimeType($url),
                'captions_url' => is_array($item) ? ($item['captions'] ?? null) : null,
                'thumbnail_url' => is_array($item) ? ($item['thumbnail'] ?? null) : null,
                'duration' => is_array($item) ? ($item['duration'] ?? 0) : 0,
            ];
        }

        return $result;
    }

    private function getMimeType($url): string
    {
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'avi' => 'video/avi',
            'mov' => 'video/quicktime',
        ];
        return $mimeTypes[$extension] ?? 'video/mp4';
    }

    // Player Control Methods
    public function updateTime($currentTime, $duration = null)
    {
        $this->currentTime = $currentTime;
        if ($duration) {
            $this->duration = $duration;
            $this->chapterDurations[$this->currentChapter] = $duration;
        }

        // Mark as completed if 95% watched
        if ($this->duration > 0 && ($currentTime / $this->duration) >= 0.95) {
            if (!in_array($this->currentChapter, $this->completedChapters)) {
                $this->completedChapters[] = $this->currentChapter;
            }
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

        // Mark current chapter as completed
        if (!in_array($this->currentChapter, $this->completedChapters)) {
            $this->completedChapters[] = $this->currentChapter;
        }

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
        $this->dispatch('setPlaybackRate', rate: $rate, playerId: $this->playerId);
    }

    public function toggleCaptions()
    {
        $this->showCaptions = ! $this->showCaptions;
        $this->dispatch('toggleCaptions', show: $this->showCaptions, playerId: $this->playerId);
    }

    // Chapter/Playlist Navigation
    public function jumpToChapter($chapterIndex)
    {
        if (isset($this->mediaData['chapters'][$chapterIndex])) {
            // If clicking the same chapter that's playing, toggle pause
            if ($this->currentChapter === $chapterIndex && $this->isPlaying) {
                $this->dispatch('pausePlayer', playerId: $this->playerId);
                return;
            }
            
            $this->currentChapter = $chapterIndex;
            $this->mediaData['current_source'] = $this->mediaData['chapters'][$chapterIndex];
            $this->currentTime = 0;
            $this->dispatch('loadNewSource', source: $this->mediaData['current_source'], playerId: $this->playerId, autoplay: true);
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
        return ! empty($this->mediaData['chapters']) &&
            $this->currentChapter < count($this->mediaData['chapters']) - 1;
    }

    public function hasPreviousChapter()
    {
        return ! empty($this->mediaData['chapters']) && $this->currentChapter > 0;
    }

    public function seekTo($time)
    {
        $this->dispatch('seekTo', time: $time, playerId: $this->playerId);
    }

    public function pausePlayer()
    {
        $this->dispatch('pausePlayer', playerId: $this->playerId);
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
                ! in_array($milestone, $this->progressMarkers)) {
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
            //     'media_type' => 'video',
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
            2 => '2x',
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

    public function getTitle(): string
    {
        return $this->mediaData['title'] ?? 'Untitled Video';
    }

    public function getDescription(): string
    {
        return $this->mediaData['description'] ?? '';
    }

    public function getChapters(): array
    {
        return $this->mediaData['chapters'] ?? [];
    }

    public function getCaptionsUrl(): string
    {
        return $this->mediaData['current_source']['captions_url'] ?? '';
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->mediaData['current_source']['thumbnail_url'] ?? $this->mediaData['thumbnail_url'] ?? null;
    }

    public function isChapterBased(): bool
    {
        return !empty($this->mediaData['chapters']);
    }
}
