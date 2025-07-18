<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use Illuminate\Support\Facades\Storage;

class AuthorBookAction extends AppComponent
{

    public function deleteBook($bookToDelete): void
    {
        if ($bookToDelete) {
            // Delete associated files
            if ($bookToDelete->cover_image_path) {
                Storage::disk('public')->delete($bookToDelete->cover_image_path);
            }
            if ($bookToDelete->pdf_file_path) {
                Storage::disk('public')->delete($bookToDelete->pdf_file_path);
            }

            $bookToDelete->delete();
            session()->flash('success', 'Book deleted successfully!');
        }
        session()->flash('success', 'Book not removed');
    }

}
