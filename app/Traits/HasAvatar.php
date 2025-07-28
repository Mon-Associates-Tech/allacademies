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
            return Storage::url($this->avatar);
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

    public function getProfileAvatarUrlAttribute(): string
    {
        return isset($this->avatar)
            ? Storage::url($this->avatar)
            : sprintf(
                'https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF&?size=256',
                urlencode($this->name)
            );
    }

    public function updateAvatar(?UploadedFile $file, $force = false): void
    {
        if (isset($file)) {
            $path = Storage::putFile('avatars', $file, 'public');
            $this->changeAvatarPath($path);
        } elseif ($force && $this->avatar) {
            $this->changeAvatarPath(null);
        }
    }

    protected function changeAvatarPath($path): void
    {
        if(!empty($path)){
            Storage::delete($this->avatar);
            $this->update(['avatar' => $path]);
            Cache::forget("avatar_{$this->id}");
        }

    }
}
