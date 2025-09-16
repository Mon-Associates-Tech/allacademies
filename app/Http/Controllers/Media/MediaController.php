<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(?int $folderId = null)
    {
        return view('media.library', compact('folderId'));
    }

    public function download(MediaFile $mediaFile)
    {
        if (!Storage::disk($mediaFile->disk)->exists($mediaFile->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk($mediaFile->disk)->download(
            $mediaFile->file_path,
            $mediaFile->original_name
        );
    }
}
