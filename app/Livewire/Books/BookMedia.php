<?php

namespace App\Livewire\Books;

use App\Models\Book;
use Livewire\Component;

class BookMedia extends Component
{
    public Book $book;

    public $currentVideoTime = 0;

    public $currentAudioTime = 0;

    public $videoVolume = 75;

    public $audioVolume = 75;

    public $videoSpeed = 1;

    public $isVideoPlaying = false;

    public $isAudioPlaying = false;

    public $chapterVideoStates = [];

    public $chapterAudioStates = [];

    public function mount(Book $book)
    {
        $this->book = $book;
        $this->initializeChapterStates();
    }

    private function initializeChapterStates()
    {
        if ($this->book->chapter_videos) {
            foreach ($this->book->chapter_videos as $index => $video) {
                $this->chapterVideoStates[$index] = [
                    'isPlaying' => false,
                    'currentTime' => 0,
                    'duration' => 0,
                ];
            }
        }

        if ($this->book->chapter_audios) {
            foreach ($this->book->chapter_audios as $index => $audio) {
                $this->chapterAudioStates[$index] = [
                    'isPlaying' => false,
                    'currentTime' => 0,
                    'duration' => 0,
                ];
            }
        }
    }

    public function toggleMainVideo()
    {
        $this->isVideoPlaying = ! $this->isVideoPlaying;
        $this->dispatch('toggle-main-video', $this->isVideoPlaying);
    }

    public function toggleMainAudio()
    {
        $this->isAudioPlaying = ! $this->isAudioPlaying;
        $this->dispatch('toggle-main-audio', $this->isAudioPlaying);
    }

    public function toggleChapterVideo($index)
    {
        $this->chapterVideoStates[$index]['isPlaying'] = ! $this->chapterVideoStates[$index]['isPlaying'];
        $this->dispatch('toggle-chapter-video', [
            'index' => $index,
            'isPlaying' => $this->chapterVideoStates[$index]['isPlaying'],
        ]);
    }

    public function toggleChapterAudio($index)
    {
        $this->chapterAudioStates[$index]['isPlaying'] = ! $this->chapterAudioStates[$index]['isPlaying'];
        $this->dispatch('toggle-chapter-audio', [
            'index' => $index,
            'isPlaying' => $this->chapterAudioStates[$index]['isPlaying'],
        ]);
    }

    public function seekVideo($time)
    {
        $this->currentVideoTime = $time;
        $this->dispatch('seek-video', $time);
    }

    public function seekAudio($time)
    {
        $this->currentAudioTime = $time;
        $this->dispatch('seek-audio', $time);
    }

    public function updateVideoVolume($volume)
    {
        $this->videoVolume = $volume;
        $this->dispatch('update-video-volume', $volume);
    }

    public function updateAudioVolume($volume)
    {
        $this->audioVolume = $volume;
        $this->dispatch('update-audio-volume', $volume);
    }

    public function updateVideoSpeed($speed)
    {
        $this->videoSpeed = $speed;
        $this->dispatch('update-video-speed', $speed);
    }

    public function rewindVideo($seconds = 10)
    {
        $this->dispatch('rewind-video', $seconds);
    }

    public function forwardVideo($seconds = 10)
    {
        $this->dispatch('forward-video', $seconds);
    }

    public function updateProgress($type, $time, $duration = null)
    {
        if ($type === 'video') {
            $this->currentVideoTime = $time;
        } elseif ($type === 'audio') {
            $this->currentAudioTime = $time;
        }
    }

    public function updateChapterProgress($type, $index, $time, $duration = null)
    {
        if ($type === 'video') {
            $this->chapterVideoStates[$index]['currentTime'] = $time;
            if ($duration) {
                $this->chapterVideoStates[$index]['duration'] = $duration;
            }
        } elseif ($type === 'audio') {
            $this->chapterAudioStates[$index]['currentTime'] = $time;
            if ($duration) {
                $this->chapterAudioStates[$index]['duration'] = $duration;
            }
        }
    }

    private function formatTime($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = floor($seconds % 60);

        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }

    public function render()
    {
        return view('livewire.books.media', [
            'hasMedia' => $this->book->single_video_file ||
                $this->book->single_audio_file ||
                ! empty($this->book->chapter_videos) ||
                ! empty($this->book->chapter_audios),
            'chapterVideosCount' => $this->book->chapter_videos ? count($this->book->chapter_videos) : 0,
            'chapterAudiosCount' => $this->book->chapter_audios ? count($this->book->chapter_audios) : 0,
        ]);
    }
}
