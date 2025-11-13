<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

trait HasAvatar
{
    public function getAvatarUrlAttribute()
    {
        if (isset($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        $key = "avatar_{$this->id}";

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        try {
            $url = 'https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF';
            $data = base64_encode(file_get_contents(sprintf($url, urlencode($this->name))));
            Cache::put($key, "data:image/png;base64,{$data}");

            return Cache::get($key);
        } catch (Exception $e) {
            return $url;
        }
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return isset($this->cover_image)
            ? Storage::disk('public')->url($this->cover_image)
            : null;
    }

    public function updateCoverImage(?UploadedFile $file, $force = false): void
    {
        if (isset($file)) {
            $path = Storage::disk('public')->putFile('covers', $file);
            $this->changeCoverImagePath($path);
        } elseif ($force && $this->cover_image) {
            $this->changeCoverImagePath(null);
        }
    }

    protected function changeCoverImagePath($path): void
    {
        if (!empty($path)) {
            // Delete old cover image if exists
            if ($this->attributes['cover_image'] && Storage::disk('public')->exists($this->attributes['cover_image'])) {
                Storage::disk('public')->delete($this->attributes['cover_image']);
            }

            $this->update(['cover_image' => $path]);
        } else {
            // Handle removal (when $path is null)
            if ($this->attributes['cover_image'] && Storage::disk('public')->exists($this->attributes['cover_image'])) {
                Storage::disk('public')->delete($this->attributes['cover_image']);
            }

            $this->update(['cover_image' => null]);
        }
    }

    public function getProfileAvatarUrlAttribute(): string
    {
        return isset($this->avatar)
            ? Storage::disk('public')->url($this->avatar)
            : sprintf(
                'https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF&?size=256',
                urlencode($this->name)
            );
    }

    public function updateAvatar(?UploadedFile $file, $force = false): void
    {
        if (isset($file)) {
            $path = Storage::disk('public')->putFile('avatars', $file);
            $this->changeAvatarPath($path);
        } elseif ($force && $this->avatar) {
            $this->changeAvatarPath(null);
        }
    }

    protected function changeAvatarPath($path): void
    {
        if (!empty($path)) {
            // Delete old avatar if exists
            if ($this->attributes['avatar'] && Storage::disk('public')->exists($this->attributes['avatar'])) {
                Storage::disk('public')->delete($this->attributes['avatar']);
            }

            $this->update(['avatar' => $path]);
            Cache::forget("avatar_{$this->id}");
        } else {
            // Handle removal (when $path is null)
            if ($this->attributes['avatar'] && Storage::disk('public')->exists($this->attributes['avatar'])) {
                Storage::disk('public')->delete($this->attributes['avatar']);
            }

            $this->update(['avatar' => null]);
            Cache::forget("avatar_{$this->id}");
        }
    }
}
